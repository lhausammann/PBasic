<?php

namespace PBasic\Interpreter\Cmd;
use PBasic\Interpreter\Parser;
use PBasic\Interpreter\Cmd\Block;

/*
 * Gosub simulates a jump to a label, executing all following statements until return, where it jumps back.
 * Because PBasic allows only jumps out of block, but not into, the GOSUB .. RETURN statement works 
 * like a regular block statement:
 * Gosub is a Block statement with no children after parsing.
 * On executing, it copies all children of the main program as its children.
 * Next is returning each child statement as regular blocks do.
  * Return in a non-function context removes the children and returns the parent of GoSub.

  * The reason is, that if GoSub happens in a block (for, while, if), return is not allowed to jump 
  * back in that block.
 */
class Gosub extends AbstractBlockStatement
{
    public $isReturning = false;

    protected $isLoop = false;

    protected $start = null;


    public function parse(Parser $parser, $basic)
    {

        $lexer = $parser->getLexer();
        $this->label = $this->matchNumber($lexer)->value;
    }

    public function execute($basic)
    {
        // set the current return label on the stack.
        $basic->addReturn($this);
    }

    public function canContinue($basic)
    {
        echo "count: " . count($this->statements);
        return count($this->statements) > 0;
    }

    public function forceEnd($basic)
    {
        // called by RETURN
        echo "forcing end";
        $this->terminate($basic);
        $this->statements = array(); // remove all statements from main
        $this->start = null;
    }


    public function next($basic) {
    	if ($this->start) {
            if ($this->start == "ended") {
                $this->start = null;
                return $this->parent->next($basic);
            }
            // because return sets the statement as "current" next will return the next statement.
            echo $this->parent;
            $next = parent::next($basic);
            // $i = $returnStat->getRoot()->getInstructionPointer($basic);
            echo $this;
            echo "nß" . $next;
            

            echo get_class($next);
            

            return $next;		 
    	}

        $basic->addReturn($this);
        $stats = $this->getRoot()->statements;
        foreach ($stats as $i => $s) {
            $s = clone $s;
            $s->setParent($this);
            if ($s->getName() == "LABEL") {
                if ($s->getLabel() == $this->label) {$label = $s; $index = $i;}
            }
            $this->statements[] = $s;
        }

        //$this->setInstructionPointer($this->getInstructionPointer($basic) , $basic);
        $this->startBlock($basic);
        $this->setInstructionPointer($index, $basic);
        //$this->getRoot()->setAsCurrent($basic, $this); // reset the current statement.
        return $this->start = $label; // = $stat;
    }
}
