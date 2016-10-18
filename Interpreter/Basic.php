<?php

namespace PBasic\Interpreter;

use PBasic\Interpreter\BasicParser;
use PBasic\Interpreter\Expression\ExpressionVisitor;
use PBasic\Interpreter\Cmd\AbstractStatement;
use PBasic\Interpreter\Scope\ValueChangingScope;
use PBasic\Interpreter\Cmd\Sub;

use PBasic\Interpreter\Exception\RuntimeException;

/*
 * Basic parses the source files to a tree structure using BasicParser during constructing.
 * runProgram() starts interpeting it on the Program root node.
 * Each statements gets the basic as a callback parameter on execturion.
 */

class Basic
{
    protected $root = null;

    private $input;
    private $lexer;
    private $currentInstr = 0;
    // indicates an abort of the current loop
    private $callStack = array();
    private $current = null;
    private $returnScope = array(); // maintain return values.

    // Screen colors of BASIC
    private $colorTable = array('black', 'blue', 'green', 'cyan', 'red', 'purple', 'brown',
        'white', 'grey', 'lightblue', 'lightgreen', 'lightcyan', '#F96D6D', 'fuchsia', 'yellow', 'white');

    private $foregroundColor = 0;
    private $backgroundColor = 7;

    private $scope = null; // holds the nestedScope instance.

    private $subs = array(); // contains defined functions
    private $observers = array(); // register an observer, which will be notified at parsing statements.

    public function __construct($file = '')
    {
        $this->lexer = new Lexer(" ");
        $this->scope = new ValueChangingScope();

        $parser = new BasicParser($this->lexer, $this);
        //echo __DIR__ . $file;
        if ($file && file_exists($file)) {
            $this->input = $input = file($file);
            $this->root = $parser->parse($input);
        } elseif(is_string($file)) {
            $this->input = $input = explode("\n", $file);
            $this->root = $parser->parse($input);
        }
    }

    public function interpret($string)
    {
        $lexer = new Lexer(" ");
        $lexer->setInput($string);
        $parser = new BasicParser($lexer, $this);
        $stat = $parser->parseLine($string);
        $stat->execute($this);

    }

    public function addSub($name, Sub $sub)
    {
        
        // do notihing -- handled by observer.
    }

    public function getSub($name)
    {
        return $this->root->getSub($name);
    }

    public function setReturnValue($value)
    {
        array_push($this->returnScope, $value);
    }

    public function getReturnValue()
    {
        $this->breakAll = false; // return value fetched by expression, RETURN ended.
        return $this->getVar('00_returnVar');
    }

    // adds a return address
    public function addReturn($parent)
    {

        $this->callStack[] = $parent;
        //$this->scope->setVar("00_callstack", $this->callsStack);

    }

    // pops a return address
    public function getReturn()
    {
        if (count($this->callStack)) {
            return array_pop($this->callStack);
        }
    }

    public function saveCallStack()
    {
        $s = serialize($this->callStack);
        $_SESSION['return_stack'] = $s;
    }

    public function loadCallStack()
    {
        if (isset($_SESSION['return_stack'])) {
            $this->callStack = unserialize($_SESSION['return_stack']);
        }
    }

    public function runProgram($stat = false)
    {
        if ($stat) {
            $stat = $stat;
        } else {
            $stat = $this->root;
        }

        $this->current = $stat; // for logging
        while ($next = $stat->next($this)) {
            $next->execute($this);
            $stat = $next;
        }
    }

    public function runFromInstructionNr($nr)
    {
        $fromStatement = $this->root->findByInstrNr($nr);
        $this->runProgram($fromStatement);
    }

    public function setBreak($break = true)
    {
    }

    public function isBreak()
    {
    }

    public function setBreakAll($bool = true)
    {
        $this->breakAll = $bool;
    }

    public function breakAll()
    {
        return $this->breakAll;
    }

    

    private function next()
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


    public function setVar($name, $value)
    {

        return $this->scope->setVar($name, $value);
    }

    public function getScope()
    {
        return $this->scope;
    }

    public function getVar($name)
    {

        return $this->scope->resolve($name);
    }

    public function hasVar($name) {
        return $this->scope->has($name);
    }


    // Visitor calls use resolve instead of getVar.
    public function resolve($name)
    {

        return $this->getVar($name);
    }

    public function setBackgroundColor($color)
    {
        $this->backgroundColor = $color;
    }

    public function getBackgroundColor($raw = false)
    {
        if (!$raw)
            return $this->colorTable[$this->backgroundColor % 15];
        return $this->backgroundColor;
    }

    public function setForegroundColor($color)
    {
        $this->foregroundColor = $color;
    }

    public function getForegroundColor($raw = false)
    {
        if (!$raw)
            return $this->colorTable[$this->foregroundColor % 15];
        return $this->foregroundColor;
    }

    public function dumpScope()
    {
        var_dump($this->scope);
        var_dump($this->gotoTable);
        var_dump($this->returnScope);
    }

    public function saveScope()
    {
        $_SESSION['scope'] = serialize($this->scope);

    }

    public function loadScope()
    {
        $this->scope = unserialize($_SESSION['scope']);
    }

    public function evaluateExpression($exprTree)
    {
        if (!$exprTree) {
            return false;
        }
        $visitor = new ExpressionVisitor($this);
        return $visitor->visit($exprTree);
    }

    public function executeFunction($fn, $args)
    {
        return $this->root->executeFunction($fn, $args, $this);
    }
    

    public function __toString()
    {
        return 'Basic ' . $this->current;
    }

    public static function run($pathOrString)
    {
        //session_start();
        $b = new Basic($pathOrString);

        if (isset($_GET['currentInstruction'])) {
            // after input proceed from given instruction Nr.
            $b->runFromInstructionNr($_GET['currentInstruction']);
        } else {
            $b->runProgram();
        }
    }
}