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
use PBasic\Interpreter\Cmd\Sub;
use PBasic\Interpreter\Cmd\End;
use PBasic\Interpreter\Cmd\Endsub;
use PBasic\Interpreter\Cmd\BIf;
use PBasic\Interpreter\Cmd\BElse;
use PBasic\Interpreter\Cmd\BEndif;
use PBasic\Interpreter\Cmd\BGoto;
use PBasic\Interpreter\Cmd\Label;



use PBasic\Interpreter\Cmd;
/**
 * Created by JetBrains PhpStorm.
 * User: Luzius Hausammann
 * Date: 17.01.14
 * Time: 18:57
 * To change this template use File | Settings | File Templates.
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
    private $instNr = 0;

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
        if ($input) {
            $this->input = $input;
        }

        if (! is_array($input)) {
            throw new Exception("Parse error: Input must be an array, each line a statement");
        }

        $program = new Program('root', 1,1,1);

        return $program->parse($this, $this->basic);
    }

    public function interpret($string, $basic)
    {
        $stat = $this->parseLine($string);
        $stat->execute($basic);
    }

    public function next()
    {
        return $this->lexer->next();
    }

    private function nextLine()
    {
        if ($this->current < count($this->input)) {
            $next = $this->input[$this->current];
            $this->current++;

            return $next;
        }

        return false; //  end of input reached.
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
    // The parent also get notified if he is registered as an observer about
    // _every_ statement parsed until it reaches the stop statement.
    public function parseUntil($stopStatement = false, $parent = null)
    {
        $stats = array();
        while ($stat = $this->nextStatement()) {
            if ($parent)
                $parent->addChild($stat);
            $this->notify($stat); // notify observers.
            if ($parent) {
                $parent->statementParsed($stat);

            }

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

    public function nextStatement()
    {
        $stat = null;
        while ($line = $this->nextLine()) {
            $line = trim($line);

            if ($this->isComment($line)) continue;
            $stat = $this->parseLine($line);
            if ($stat) {
                break;
            }
        }

        return $stat;
    }

    private function parseLine($line)
    {
        $line = trim($line);
        if ($line) {
            $statement = $this->createStatement($line);

            return $statement;
        }
    }

    private function isComment($line)
    {
        foreach ($this->comments as $commentSign) {
            // TODO: Fix this
            if (stripos ($line, $commentSign ) !== false) {
                return true;
            }
        }

        return false;
    }

    private function createStatement($line)
    {
        $cmdNs = "PBasic\\Interpreter\\Cmd\\";

        $className = "";
        $statements = $this->lexer->setInput($line);
        $statement = $this->lexer->next();

        $statementName = $statement->value;
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
        $fullQualifiedName = $cmdNs . $className;
        $stat = new $fullQualifiedName(strtoupper($statementName), $this->instrNr, $lineNr, $this->currentInstr);
        var_dump($stat);
        $stat->parse($this, $this->basic);
        echo $cmdNs . $className;
        return $stat;
    }

    private function getCmdClass($className)
    {
        $ns = "//PBasic//Interpreter//Cmd//";

        echo $className;
        $reserved = array('Print', 'If', 'While', 'Break', 'Goto', 'For', 'Return', 'Endif', 'Else');
        if (in_array($className, $reserved)) {

            return 'B' . $className; // Prefix classname to not conflict with php keywords.
        }

        // Basic shortcut for print
        if ($className == '?') {
            return 'BPrint';
        }

        // No class found

        if (! class_exists($ns . $className)) {
            echo $className . " not found. ";
            $className = '';
        }
        echo "return " . $className;
        return $className;
    }
}

