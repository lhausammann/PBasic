<?php
namespace PBasic\Interpreter;
use PBasic\Interpreter\Cmd\AbstractStatement;
use PBasic\Interpreter\Expression\ExpressionParser;
use PBasic\Interpreter\Expression\Token;
use PBasic\Interpreter\Lexer;


/** Those commands are used by new $cmd */
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
use PBasic\Interpreter\Cmd\Label;
use PBasic\Interpreter\Cmd\Data;
use PBasic\Interpreter\Cmd\Read;
use PBasic\Interpreter\Cmd;

/*
 * Basic parser is the main entry point for the parsing process.
 * Most of the process is delegated to the  statements classes.
 * To allow that 
 * - blocks can hook in the process using "parseUntil" and are notified when the end statement is 
 * found on the same block (e.g. if parsing multiple nested ifs, each one is notified by reaching
 * the belonging endif )
 * - for more flexiblility, observers can be attached and removing during parser. Parent blocks get  * notified by parsing of each statements, even if nested. With that approach its possible for a  * parent SUB statement all enclosed RETURN statements.

  * Expression are parsed by a separate ExpressionParser. But the parser exposes that functionality by parseExpression().

 * The parser also allows some shortcuts for the lexing and is given to each statement as an argument.

 */


class BasicParser implements Parser
{
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
    private $instrNr = 0;

    private $basic;
    private $observers = array();

    public function __construct($lexer, $basic)
    {
        $this->lexer = new Lexer(" ");
        $this->basic = $basic;
    }

    public function setInput($input)
    {
        $this->input = $input;
    }

    public function getLexer()
    {
        return $this->lexer;
    }

    public function parse($input = null)
    {
        $input = implode($input, ':') . ':';

        if ($input) {
            $this->input = $input;
        }
        $this->lexer = new Lexer($input);
        // set up the program
        $program = new Program('root', 1, 1, 1);
        return $program->parse($this, $this->basic);
    }

    public function interpret($string, $basic)
    {

    }

    public function next()
    {

        return $this->lexer->next();
    }

    private function nextLine()
    {

        $next = $this->input[$this->current];
        $this->current++;

        return $next;
    }

    // registers an observer during the parse process.
    public function addObserver(AbstractStatement $statement)
    {
        $this->observers[] = $statement;
    }

    public function removeObserver(AbstractStatement $statement)
    {
        $observers = array();
        foreach ($this->observers as &$observer) {
            if ($observer === $statement) {
                continue;
            }
            $observers[] = $observer;
        }

        $this->observers = $observers;
    }

    public function notify($statement)
    {
        foreach ($this->observers as $observer) {
            $observer->update($statement);
        }
    }

    public function matchExpression()
    {
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
    public function parseUntil($stopStatement = false, $parent = null)
    {
        $stats = array();
        while ($stat = $this->nextStatement()) {

            if ($parent) {
                if ($stat->isExecutable()) {
                    $parent->addChild($stat);
                }
                // call back the parent block about parsing
                $parent->statementParsed($stat);
            }


            $this->notify($stat); // notify observers about parsed statements.

            $stats[] = $stat;

            if (strtoupper($stat->getName()) === strtoupper($stopStatement)) {
                break;
            }
        }

        if ($parent) {
            // call back the parent
            $parent->endBlock($stat);
        }

        return $stats;
    }

    public function nextStatement()
    {
        $next = $this->lexer->next();
        if (!$next) {
            return null;
        }
        // skip ::::
        while ($next->type == Token::DOUBLE_POINT) {
            $next = $this->next();
        }
        while ($this->isComment($next->value)) {
            $next = $this->next();
        }

        if ($next->value == "'") {
            // skip comment
            while ($next->value != ':') {
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

    private function createStatement($statement)
    {

        $ns = "PBasic\\Interpreter\\Cmd\\";
        $statementName = $statement->value;

        // 10 PRINT "Hi"
        // ^----- Lexer
        if ($statement->type == Token::NUMBER) {
            $label = new Label($statement->value);
            return $label;
        }

        $upper = strtoupper($statementName{0});
        $lower = strtolower(substr($statementName, 1));
        $className = $this->getCmdClass($upper . $lower);
        if (!$className) {

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

    private function getCmdClass($className)
    {
        $ns = "PBasic\\Interpreter\\Cmd\\";
        $reserved = array('Print', 'If', 'While', 'Break', 'Goto', 'For', 'Return', 'Endif', 'Else', 'Continue');
        if (in_array($className, $reserved)) {
            return 'B' . $className; // Prefix classname to not conflict with php keywords.
        }

        // Basic shortcut for print
        if ($className == '?') {
            return 'BPrint';
        }

        // No corresponding statement found means expression.
        if (!class_exists($ns . $className, true)) {

            $className = '';
        }

        return $className;
    }

    private function isComment($value)
    {
        foreach ($this->comments as $sign) {
            if (stripos($sign, $value) !== false) {
                while ($next = $this->lexer->next()) {
                    if ($next->value == ':') {
                        break;
                    }
                }
                return true;
            }
        }
        return false;
    }
}


