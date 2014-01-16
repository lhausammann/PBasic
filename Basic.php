<?php
//require_once('Lexer.php');
require_once 'Expression/ExpressionParser.php'; // evaluate expressions
require_once 'Expression/ExpressionVisitor.php';
require_once 'Scope.php';
require_once 'Parser.php';

class Basic
{
    protected $root = null;

    private $input;
    private $lexer;
    private $currentInstr = 0;
    private $cmds = array();
    // indicates an abort of the current loop
    private $isLoopBreak = false;
    private $callStack = array();
    private $current = null;

    private $returnScope = array(); // maintain return values.
    // indicates a jump/return which breaks all loops.
    private $breakAll = false;

    // Screen colors of BASIC
    private $colorTable = array('black', 'blue', 'green', 'cyan', 'red', 'purple', 'brown',
    'white', 'grey', 'lightblue', 'lightgreen', 'lightcyan', '#F96D6D', 'fuchsia', 'yellow', 'white');

    private $foregroundColor = 0;
    private $backgroundColor = 7;

    private $scope = null; // holds the nestedScope instance.

    private $gotoTable = array(); // line numbers for GOTO
    private $subs = array(); // contains defined functions
    private $observers = array(); // register an observer, which will be notified at parsing statements.

    public function __construct($file = '')
    {
        $this->lexer = new Lexer(" ");
        $this->scope = new NestedScope();

        $parser = new BasicParser($this->lexer, $this);

        if ($file) {
            //$this->interpret("LET __parsetime = microtime(true)");
            $this->input = $input = file($file);
            $this->root = $parser->parse($input);
        }

    }

    public function interpret($string)
    {
        $lexer = new Lexer(" ");
        $lexer->setInput($string);
        $parser = new BasicParser($lexer, $this);

        $parser->interpret($string, $this);

    }

    public function addLabel($label, $instNr)
    {
        $this->gotoTable[$label] = $instNr - 1; // point to label statement
    }

    public function hasLabel($label)
    {
        return isset($this->gotoTable[$label]);
    }

    // called by GOTO statement
    public function jump($label)
    {
        $this->breakAll = true; // terminate all loops
        // set control flow to the specified label
        $this->currentInstr = $this->gotoTable[$label];
    }

    // called by LABEL statement DEPRECATED
    public function reachedLabel()
    {
        $this->breakAll = false; // execute loops again.
        $this->isLoopBreak = false;
    }

    public function addSub($name, Sub $sub)
    {
        $this->subs[$name] = $sub;
    }

    public function getSub($name)
    {
        $fn = null;
        if ((array_key_exists($name, $this->subs))) {
            $fn = $this->subs[$name];
        }
        if (! $fn) {
            throw new Exception ('Function ' . $name . 'not defined.');
        }

        return $fn;
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
        $this->isLoopBreak = $break;
    }

    public function isBreak()
    {
        return $this->isLoopBreak;
    }

    public function setBreakAll($bool = true)
    {
        $this->breakAll = $bool;
    }

    public function breakAll()
    {
        return $this->breakAll;
    }

    // Indicates wether a loop (while/for statement) should continue
    // with the execution.
    public function canContinue()
    {
        return (! $this->isLoopBreak) &&  (! $this->breakAll);
    }

    // runs a block of statements
    public function runBlock($statements)
    {
        /*
        if ($this->breakAll()) {
            return;
        }*/

        foreach ($statements as $statement) {
            $statement->execute($this);
            /*
            if ($this->isBreak() || $this->breakAll()) { // break, return or goto encountered.
                // break current loop. FOR or WHILE has to reset break after terminating.
                return;
            }*/
        }
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
        return $this->scope->getVar($name);
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
        if (! $raw)
            return $this->colorTable[$this->backgroundColor % 15];
        return $this->backgroundColor;
    }

    public function setForegroundColor($color)
    {
        $this->foregroundColor = $color;
    }

    public function getForegroundColor($raw = false)
    {
        if (! $raw)
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
        if (! $exprTree) {
            return false;
        }
        $visitor = new ExpressionVisitor($this);

        return $visitor->visit($exprTree);
    }

    public function executeFunction($fn, $args)
    {
        // is it a user definded SUB in code?
        if (array_key_exists($fn, $this->subs)) {
            $function = $this->subs[$fn];
            $function->setArguments($args, $this);
            // call it
            $function->start(null, $this);

            $ret = $function->executeSub($args, $this); // execute it, put return value on top

            return $ret;

        }

        $mappings = array(
            'SQR' => 'sqrt',
            'INT' => '_toInt',
            'MOD' => '_mod'
            );
            if (array_key_exists($fn, $mappings)) {
                $fn = $mappings[$fn];
            }

            return call_user_func_array($fn, $args);
    }

    public function mod($a, $b)
    {
        return _mod($a,$b);
    }

    public function __toString()
    {
        return 'Basic ' . $this->current;
    }

    public static function run($file)
    {
        //session_start();
        $b = new Basic($file);
        if (isset($_GET['currentInstruction'])) {

            $b->runFromInstructionNr($_GET['currentInstruction']);
        } else {
            $b->runProgram();
        }
    }

}

// custom functions
// for the basic interpreter
function _toInt($toInt)
{
    return (int) $toInt;
}

function _mod($a, $b)
{
    return $a % $b;
}

function rnd($max = null, $min = null)
{
    if ($max !== null && $min !== null) {
        return (int) rand($min, $max);
    } elseif ($max) {
        return (int) rand(0, $max);
    }

    return rand(0,1000) / 1000;
}
