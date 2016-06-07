<?php

namespace PBasic\Interpreter\Cmd;
use PBasic\Interpreter\Parser;
use PBasic\Interpreter\Cmd\Block;

/*
 * Gosub simulates a jump to a label, executing all following statements until return, where it jumps back.
 * Because basic allows only jumps out of block, but not into, the GOSUB .. RETURN statement works like that:
 * Gosub is a Block statement with no children first, because they are added during parsing.
 * On executing, it copies all children of the main program as its children.
 * Next is returning each child statement as regular blocks do.
  * Return in a non-function context removes the children and returns the parent of GoSub.

  * The reason is, that if GoSub happens in a block (for, while, if), return is not allowed to jump back.
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

        return count($this->statements) > 0;
    }

    public function forceEnd($basic)
    {
        // called by RETURN
        $this->setInstructionPointer(1, $basic);

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

            echo "nß" . $next;
            

            echo get_class($next);
            

            return $next;		 
    	}

        $basic->addReturn($this);
        echo "da!" . $this->getInstructionPointer($basic);

        
        //$this->setInstructionPointer($i, $basic);

        //$stat->setParent($this);
        echo $this;
        $stats = $this->getRoot()->statements;
        foreach ($stats as $i => $s) {
            $s = clone $s;
            $s->setParent($this);
            if ($s->getName() == "LABEL") {
                if ($s->getLabel() == $this->label) {$label = $s; $index = $i;}
            }
            $this->statements[] = $s;
        }
        //$this->statements = $this->getRoot()->statements;

        $this->isReturning = false;
        $this->setInstructionPointer($this->getInstructionPointer($basic) + 1, $basic);
        $this->startBlock($basic);
        $this->setInstructionPointer($index, $basic);
        //$this->getRoot()->setAsCurrent($basic, $this); // reset the current statement.
        return $this->start = $label; // = $stat;
    }
}
