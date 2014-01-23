<?php
namespace PBasic\Interpreter;
use PBasic\Interpreter\Cmd\AbstractStatement;
use PBasic\Interpreter\Expression\ExpressionParser;
use PBasic\Interpreter\Expression\Token;
use PBasic\Interpreter\Lexer;



use PBasic\Interpreter\Cmd\AbstractBlockStatement;
use PBasic\Interpreter\Cmd\Block;

use PBasic\Interpreter\Cmd\Let;
use PBasic\Interpreter\Cmd\Wend;
use PBasic\Interpreter\Cmd\BWhile;
use PBasic\Interpreter\Cmd\BPrint;
use PBasic\Interpreter\Cmd\Input;
use PBasic\Interpreter\Cmd\BFor;
use PBasic\Interpreter\Cmd\Next;
use PBasic\Interpreter\Cmd\Color;
use PBasic\Interpreter\Cmd\Sub;
use PBasic\Interpreter\Cmd\End;
use PBasic\Interpreter\Cmd\Endsub;
use PBasic\Interpreter\Cmd\BIf;
use PBasic\Interpreter\Cmd\BElse;
use PBasic\Interpreter\Cmd\BEndif;
use PBasic\Interpreter\Cmd\BGoto;

/** FIXME */
require_once __DIR__ . '/Cmd/End.php';
// require_once __DIR__ . '/Cmd/Color.php';

use PBasic\Interpreter\Cmd\Label;

use PBasic\Interpreter\Cmd;

class BasicParser implements Parser {
    private $comments = array(
        'REM',
        "'",
        "//",
        "",
    );

    private $input;
    private $lexer;
    private $current = 0;

    private $currentInstr = 0;
    private $instNr = 0;

    private $basic;
    private $observers = array();

    public function __construct($lexer, $basic) {
        $this->lexer = new Lexer(" ");
        $this->basic = $basic;
    }

    public function setInput($input) {
        $this->input = $input;
    }

    public function getLexer() {
        return $this->lexer;
    }

    public function parse($input = null) {
        $input = implode($input, ':') . ':';

        if ($input) {
            $this->input = $input;
        }
        $this->lexer = new Lexer($input);
        // set up the program
        $program = new Program('root', 1,1,1);
        return $program->parse($this, $this->basic);
    }

    public function interpret($string, $basic) {

        $stat = $this->parseLine($string);
        $stat->execute($basic);
    }

    public function next() {

        return $this->lexer->next();
    }

    private function nextLine() {

        $next = $this->input[$this->current];
        $this->current++;

        return $next;
    }

    // registers an observer during the parse process.
    public function addObserver(AbstractStatement $statement) {
        $this->observers[] = $statement;
    }

    public function removeObserver(AbstractStatement $statement) {
        $observers = array();
        foreach ($this->observers as &$observer) {
            if ($observer === $statement) {
                continue;
            }
            $observers[] = $observer;
        }

        $this->observers = $observers;
    }

    public function notify($statement) {
        foreach ($this->observers as $observer) {
            $observer->update($statement);
        }
    }

    public function matchExpression() {
        $exprParser = new ExpressionParser($this->lexer);
        try {
            $tree = $exprParser->matchExpression();
            // configure the lexer to return the parsers current lookahead as next token:
            $this->lexer->setNext($exprParser->getLookahead());
        } catch (Exception $e) {

            throw new Exception("Parse error in expression on line: " . $this->current . ': ' . $e->getMessage());
        }
        return $tree;
    }

    // parses a file or a block until the statement stopStatement (or to the end, if none given).
    // if a parent statement is given, it will be notified by parseStatement about
    // each statement parsed in this block. (not about parsed statements in a block, e.g
    // an else statement in an if block. Parent only gets notified about if.)
    // The parent also get notified if its registered as an observer about
    // _every_ statement parsed until it reaches the stop statement.
    public function parseUntil($stopStatement = false, $parent = null) {
        $stats = array();
        while($stat = $this->nextStatement()) {

            if ($parent) {
                if ($stat->isExecutable()) {
                    $parent->addChild($stat);
                }
                $parent->statementParsed($stat);
            }
            $this->notify($stat); // notify observers.

            $stats[] = $stat;

            if (strtoupper($stat->getName()) === strtoupper($stopStatement)) {
                break;
            }

            if (! $stopStatement) {
                $this->currentInstr++;
            }
        }

        if ($parent) {
            // notify the parent of end of parsing.
            // Parsed stopStatement is given back to complete block parsing.
            $parent->endBlock($stat);
        }

        return $stats;
    }

    public function nextStatement() {
        $next = $this->lexer->next();
        if (! $next) {
            return null;
        }
        // skip ::::
        while($next->type == Token::DOUBLE_POINT) {
            $next = $this->next();
        }

        if ($next->value=="'") {
            // skip comment
            while($next->value != ':') {
                $next = $this->next();
            }

            $next = $this->nextStatement();

            return $next;
        }

        if ($next->type == Token::END) {

            return null;
        }

        $stat = null;
        if ($next) {
            $stat = $this->createStatement($next);
        }

        return $stat;
    }

    private function parseLine($line) {
        $stat = $this->lexer->next();

        //$stat = $cmdToken->value;
        if ($line) {
            $statement = $this->createStatement($stat);
            return $statement;
        }
    }

    private function isComment($line) {
        foreach ($this->comments as $commentSign) {
            // TODO: Fix this
            if (stripos ($line, $commentSign ) !== false) {
                return true;
            }
        }
        return false;
    }

    private function createStatement($statement) {
        $ns = "PBasic\\Interpreter\\Cmd\\";
        $statementName = $statement->value;
        if ($statementName == ':' ) {
            return null;
        }

        // 10 PRINT "Hi"
        // ^----- Lexer
        if ($statement->type == Token::NUMBER) {
            $label = new Label($statement->value);
            return $label;
        }

        $upper = strtoupper($statementName{0});
        $lower = strtolower(substr($statementName,1));
        $className = $this->getCmdClass($upper . $lower);
        if (! $className) {

            // let is optional keyword
            $className = 'Let';
            $this->lexer->setNext($statement); // put statement back
        }
        $lineNr = $this->current;
        $this->instrNr++;
        $class = $ns . $className;
        $stat = new $class (strtoupper($statementName), $this->instrNr, $lineNr, $this->currentInstr);

        $stat->parse($this, $this->basic);
        return $stat;
    }

    private function getCmdClass($className) {
        $ns = "PBasic\\Interpreter\\Cmd\\";
        $reserved = array('Print', 'If', 'While', 'Break', 'Goto', 'For', 'Return', 'Endif', 'Else');
        if (in_array($className, $reserved)) {
            return 'B' . $className; // Prefix classname to not conflict with php keywords.
        }

        // Basic shortcut for print
        if ($className == '?') {
            return 'BPrint';
        }

        // No class found
        if (! class_exists($ns . $className, true)) {
            echo 'class not found: ' . var_dump($ns . $className);
            $className = '';
        }

        return $className;
    }
}


