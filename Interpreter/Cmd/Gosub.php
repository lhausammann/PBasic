<?php

namespace PBasic\Interpreter\Cmd;
use PBasic\Interpreter\Parser;
use PBasic\Interpreter\Cmd\Block;


class Gosub extends BGoto
{
    public $isReturning = false;

    public function execute($basic)
    {
        // set the current return label on the stack.
        $basic->addReturn($this);
    }

    public function next($basic) {
    	if ($this->isReturning) {
            $this->isReturning = false;
            echo "INSTRUCTIONPTR:" . $this->parent->getInstructionPointer($basic);

            // because return sets the statement as "current" next will return the next statement.
            $next = $this->parent->next($basic);
            return $next;
    		 
    	}

        $this->isReturning = false;
        return $this->getRoot()->jump($this->label, $basic);
    }
}
