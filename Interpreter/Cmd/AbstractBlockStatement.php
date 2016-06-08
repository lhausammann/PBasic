<?php
namespace PBasic\Interpreter\Cmd;
use PBasic\Interpreter\Cmd\AbstractStatement;


/**
 * SUMMARY
 * A basic program consists of block statements / regular statements.
 * AbstractBlockStatements 1. delegates the parsing process to the child statements (usually using parseUntil) AND 2. manages the execution flow.

 * Each parsing of a Program starts with the Program statement, which parses 
 * all statements - delegating to blocks and regular statements. 
 * 
 * PARSING
 * Each concrete implementation ofAbstractBlockStatement (e.g. FOR) parses 
 * its child sttements. 
 * After parsing, each program consists of a list of regular statements 
 * and block statements. 
 * @see AbstractStatement->ParseUntil, BasicParser->parse

 * EXECUTION FLOW
 * By iterating over that list, each statement is executed using execute() and 
 * after that next() is called. Normal statements delegate the next call to 
 * the parent, which must be a block statement.
 * Block statements normally don't implement execute() and manage to return a * subsequent child statement when called next() each time, after the block 
 * finishes.
 * After finishing, they usually return the parent (which must be a block 
 * itself) which calls the statement afterwards.
 * Each block maintains its state using a pointer (current) to the currently 
 * executed statement. (To allow this state during inputs, this pointer is 
 * a system variable in the current scope. Note that in basic scopes only 
 * exists for program and SUBs.)
 * AbstractBlockStatement contains 
 * - parsing shortcuts for the parsing process e.g parseUntil to parse until 
 *   the given end statement
 * - a next() method to return the next statement. A block always returns a 
 * regular statement, never a block when next is called.

 * Note that GOTO is not easy to implement using that block approach (SUB and GOSUB are easier.)
 */


abstract class AbstractBlockStatement extends AbstractStatement
{

    protected $isLoop = false;
    protected $current = 0;

    public function isLoop()
    {
        return $this->isLoop;
    }


    public function getChildren()
    {
        return $this->statements;
    }

    public function canContinue($basic)
    {

        return count($this->statements) > $this->getInstructionPointer($basic);
    }


    public function addChild($statement)
    {
        $statement->setParent($this);
        $this->statements[] = $statement;
    }


    public function startBlock($basic)
    {
        // set the instrction pointer to current scope
        $this->setInstructionPointer(0, $basic);
        $this->current = 0;
    }



    public function next($basic)
    {

        $this->current = $this->getInstructionPointer($basic);
        if ($this->isLoop && $this->canContinue($basic)) {
            $this->current = 0; // reset counter
            $this->setInstructionPointer(0, $basic);
        }


        if ($this->current === null) {
            $this->startBlock($basic);
        }

        if ($this->canContinue($basic)) {

            $stat = $this->statements[$this->current];
            $this->current++;
            
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

    public function endBlock($stat)
    {
        // hook here to take action after parsing ends of block.
    }

    /**
     * Forces the given statement to be handled as  it is currently executing.
     * This forces the call on "next" to happen on that statement.
     * Note that if you use setAsCurrent in the next() call of a statement you will cause an * endless loop, because the call forces the parent to return the same statement as next.
     * This is needed for GOTO 
     * TODO: Rename to setAsNext() because that is what happens. (next returns the staement at current position and advances the current index afterwards).
     */
    public function setAsCurrent($basic, $stat = null, $statements = array())
    {
        $found = false;
        $statements = count($statements) ? $statements : $this->statements;
        foreach ($statements as $i => $statement) {
            if ($stat == $statement) {
                $found = true;
                break;
            }
        }
        if (!$found) {
            throw new \Exception("Could not find statement " . $stat->getName() . ' ' . $stat->errorInfo());
        }
        $this->current = $i;
        $this->setInstructionPointer($i, $basic);
        return $stat;
    }


    public function terminate($basic)
    {
        $this->current = 0;
        $this->setInstructionPointer(0, $basic);
        return $this->parent;
    }

    public function terminateAll($basic)
    {
        $this->terminate($basic); // terminate current block
        $p = $this->parent;
        while (null != $p) {
            $p->terminate($basic); // terminate parents
            $p = $p->parent;
        }

        return $this->parent;
    }
    
    /*
     * Fint the given statement in the list of the child statements, going 
     *recursively through all children.
     */
    
    public function findByInstrNr($nr, $basic = null)
    {
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

    /**
     * Sets a "system" variable by prefixing it with the current number of the * block on the current scope.
     * A nice side-effect of this is, its a. easy to manage nested blocks and 
     * b. impossible to read this variable from a basic program (because e.g. 
     * 17_iPtr is not parseable).
     */

    public function getInstructionPointer($basic)
    {
        $var = $this->nr . '_iPtr';

        $scope = $basic->getScope();

        if ($scope->has($var)) {
            return $scope->getVar($var);
        }

        return null; // no instruction pointer left means scope does not exist anymore.
    }

    protected function setInstructionPointer($i, $basic)
    {
        $var = $this->nr . '_iPtr';
        if (!is_object($basic)) {
            throw new Exception ("Basic not given, but: " . $basic);
        }
        $scope = $basic->getScope();
        $scope->setVar($var, $i);
    }
}