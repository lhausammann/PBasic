<?php
namespace PBasic\Interpreter;

use PBasic\Interpreter\Cmd\AbstractBlockStatement;
use PBasic\Interpreter\Exception\RuntimeException;

class Program extends AbstractBlockStatement
{
    private $gotoTable = array();
    private $subs = array();

    public function parse(Parser $parser, $basic)
    {
        $parser->addObserver($this);
        $parser->parseUntil('', $this);
        $parser->removeObserver($this);
        return $this;
    }

    public function update($stat)
    {
        if ($stat->getName() == 'LABEL') {

            $this->gotoTable[$stat->getLabel()] = $stat;
        } elseif ($stat->getName() == "SUB") {
//            echo "register: " . $stat->getSubName();
            $this->subs[strtolower($stat->getSubName())] = $stat;
        }   
    }

    public function getSub($name) {
        $fn = null;
        $name = strtolower($name);
        if ((array_key_exists($name, $this->subs))) {
            $fn = $this->subs[$name];
        }
        if (!$fn) {
            throw new Exception ('Function ' . $name . ' not defined.');
        }

        return $fn;
    }

    /* execute function is used by ExpressionParser and needs some callbacks to basic.
     */
    public function executeFunction($fn, $args, $basic) {
        $fn = ($fn);
        // is it a user array in code (looks same as function token).
        if ($basic->hasVar($fn)) {
            // must be an array.
            return $basic->getVar($fn)[$args[0]];
        }
        // is it a user definded SUB in code?
        
        if (array_key_exists(strtolower($fn), $this->subs)) {
            $function = $this->getSub($fn);
            $function->setArguments($args, $basic);
            $function->start(null, $basic);

            $ret = $function->executeSub($args, $basic); // execute it, put return value on top
            return $ret;

        }

        $mappings = array(
            'SQR' => 'sqrt',
            'INT' => '_toInt',
            'MOD' => '_mod',
            'mod' => '_mod',
        );
        if (array_key_exists($fn, $mappings)) {
            $fn = $mappings[$fn];
        }

        if (function_exists(__NAMESPACE__ . '\\' . $fn)) {
            return call_user_func_array(__NAMESPACE__ . '\\' . $fn, $args);
        } else if (function_exists($fn)) {
            return call_user_func_array($fn, $args);
        }
        //$basic->dumpScope();
        print_r (array_keys($this->subs));
        throw new RuntimeException('Could not resolve ' . $fn . ' to a function, parser function or defined sub. Registered subs were:' . implode(array_keys($this->subs), ', '));
    }

    public function execute($basic)
    {
    }

    public function jump($label, $basic)
    {
        $stat = $this->gotoTable[$label];
        return $stat->setAsCurrent($basic, $stat);
    }

    public function mod($a, $b)
    {
        return _mod($a, $b);
    }

}

    // custom functions
// for the basic interpreter
function _toInt($toInt)
{
    return (int)$toInt;
}

function _mod($a, $b)
{
    return $a % $b;
}

function rnd($max = null, $min = null)
{
    if ($max !== null && $min !== null) {
        return (int)rand($min, $max);
    } else if ($max) {
        return (int)rand(0, $max);
    }

    return rand(0, 1000) / 1000;
}

function sgn($nr)
{
    return $nr >= 0;
}


