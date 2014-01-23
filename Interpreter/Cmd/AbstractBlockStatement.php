<?php
namespace PBasic\Interpreter\Cmd;
use PBasic\Interpreter\Cmd\AbstractStatement;

abstract class AbstractBlockStatement extends AbstractStatement {

    protected $isLoop = false;
    protected $statements;
    protected $current = 0;

    public function isLoop() {
        return $this->isLoop;
    }


    public function getChildren() {
        return $this->statements;
    }

    public function canContinue($basic)  {
        return $basic->canContinue()
            && count($this->statements) > $this->current;
    }


    public function addChild($statement) {
        $statement->setParent($this);
        $this->statements[] = $statement;
    }



    public function startBlock($basic) {
        // set the instrction pointer to current scope
        $this->setInstructionPointer(0, $basic);
        $this->current = 0;
    }

    public function next($basic) {
        $this->current = $this->getInstructionPointer($basic);
        if ($this->isLoop && (count($this->statements)) <= $this->current) {
            $this->current = 0; // reset counter
            $this->setInstructionPointer(0, $basic);
        }


        if ($this->current === null) {
            $this->startBlock($basic);
        }

        if ($this->canContinue($basic)) {
            $stat = $this->statements[$this->current];
            $this->current++;;
            $this->setInstructionPointer($this->current, $basic);

            // next always returns leave statements, never
            // blocks
            if ($stat instanceOf AbstractBlockStatement) {
                $stat = $stat->next($basic);
            }

            return $stat;
        } else {
            // end of block reached.
            $this->current = 0;

            if ($this->parent) {
                return $this->parent->next($basic);
            }

            return null; // End of program reached.
        }
    }

    public function endBlock($stat) {
        // hook here to take action after parsing ends of block.
    }

    public function setAsCurrent($basic, $stat = null) {
        $found = false;
        foreach ($this->statements as $i => $statement) {
            if ($stat == $statement) {
                $found = true;
                break;
            }
        }
        if (! $found) {
            throw new Exception("Could not find statement " . $stat->getName() . ' ' . $stat->errorInfo());
        }
        $this->current = $i;
        $this->startBlock($basic);
        $this->setInstructionPointer($i, $basic);

        return $stat;
    }

    public function terminate($basic) {
        $this->current = 0;
        $this->setInstructionPointer(0, $basic);
    }

    public function terminateAll($basic) {
        $this->terminate($basic); // terminate current block
        $p = $this->parent;
        while (null != $p) {
            $p->terminate($basic); // terminate parents
            $p = $p->parent;
        }
    }

    public function findByInstrNr($nr, $basic = null) {
        if (!$this->statements) {
            return false;
        }

        foreach ($this->statements as $i => $stat) {
            if ($s = $stat->findByInstrNr($nr)) {
                $this->current = $i + 1; // set counter to next statement
                return $s;
            }
        }

        return false;
    }

    protected function getInstructionPointer($basic) {
        $var = $this->nr . '_iPtr';

        $scope = $basic->getScope();

        if ($scope->has($var)) {
            return $scope->getVar($var);
        }

        return null; // no instruction pointer left means scope does not exist anymore.
    }

    protected function setInstructionPointer($i, $basic) {
        $var = $this->nr . '_iPtr';
        if (! is_object($basic)) {
            throw new Exception ("Basic not given, but: " . $basic);
        }
        $scope = $basic->getScope();
        $scope->setVar($var, $i);


        //throw new Exception('Could not found instrcuction pointer in Scope');

    }
}