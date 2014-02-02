<?php
namespace PBasic\Interpreter\Cmd;
use PBasic\Interpreter\Cmd\AbstractStatement;
use PBasic\Interpreter\Expression\Token;
use PBasic\Interpreter\Parser;

class Sub extends AbstractBlockStatement  {

    protected $isExecutable = true;

    private $name;
    private $block = array();
    private $params = array();
    private $returnVar = '';
    private $returnValue = '';
    private $isStarted = false;
    private $paramNames = array();

    const INSTRUCTION_POINTER = '00_instructionPointer';
    const RETURN_ADDRESS = '__returnAddress';

    public function parse(Parser $parser, $basic) {
        $this->name = $this->matchIdentifier($parser)->value;
        $this->createIdentifierList($parser);
        $parser->addObserver($this);
        $this->block = $this->statements = $parser->parseUntil('ENDSUB', $this);
        $parser->removeObserver($this);
        $basic->addSub($this->name, $this);
    }

    public function setReturnVar($var) {
        $this->returnVar = $var;
    }

    public function setReturnValue($value) {
        $this->returnValue = $value;
    }

    /**
     * Callback of parseUntil
     * @param $statement
     */
    public function endBlock($statement) {
        $this->parent = null; // remove parent.
        $this->assertClass('ENDSUB', $statement);
    }

    /**
     * The block must now the current state
     * @param $basic
     * @return bool
     */
    public function isExecuting($basic) {

        return $this->getInstructionPointer($basic) > 0;
    }

    public function isStart($basic) {

        return $this->getInstructionPointer($basic) == 0;
    }

    public function isEnd($basic) {

        return $this->getInstructionPointer($basic) >= count($this->statements);
    }

    public function forceEnd($basic) {
        // called by RETURN
        $this->current = count($this->statements) -1; // move to ENDSUB statement
        $this->setInstructionPointer($this->current, $basic);

    }

    /**
     * Called by the attached observer.
     * Sub is notified of each parsed statement, regardless of block level dedpth
     * @param $statement
     */
    public function update($statement) {
        if ($statement->getName() === "RETURN") {
            $this->hasReturn($statement);
            $statement->setFunction($this);
        }
        $this->checkForbidden($statement);
    }

    public function hasReturn($return) {
        $this->hasReturn = true;
    }


    private function checkForbidden($statement) {
        if ($statement->getName() === "SUB") {
            throw new Exception("No nested SUBs allowed." . $statement->errorInfo());
        }
    }

    public function start($caller, $basic) {
        if ($caller) {
            $this->setParent($caller->parent); // Parent needs to be reset to CALLs Parent.
        } else {

            $this->parent = null;
        }

        $this->addScope($this->params, $basic);
        $basic->getScope()->setVar('__started', true);
        // $this->isStarted = true;
        //$basic->setVar(self::RETURN_ADDRESS, $this->parent);
        // manage the instruction pointer on the scope to handle recursion.
        //$basic->setVar('00_instructionPointer', 0);
    }

    /**
     * Execute the whole sub function at once.
     * This is used by the ExpressionParser, and no input statements are allowed.
     * @param $params
     * @param $basic
     * @return string
     * @throws Exception
     */
    public function executeSub($params, $basic) {

        // check, if parameters match correctly
        if (count($params) === count($this->paramNames)) {
            $this->setArguments($params, $basic);
            // execute this function
            $stat = $this->next($basic);
            while($stat) {
                $stat->execute($basic);
                $stat = $stat->next($basic);
                if (get_class($stat) == 'Endsub') {

                    throw new Exception('No return statement found in sub: '. $this->errorInfo());
                }
            }

            $this->parent = null;

            $ret = $this->returnValue;
            return $ret;

        } else {
            throw new Exception("Parameters not matching." . $this->errorInfo());
        }
    }

    public function execute($basic) {
        // Nothing to do in this case (function block only gets executed by its CALL block or by an expression.
        throw new Exception("Should not happen");
    }

    public function setArguments($params, $basic) {
        $this->params = $params;
    }

    private function createIdentifierList($lexer) {
        $token = $lexer->next();
        if ($token->type === Token::DOUBLE_POINT) {
            return;
        } else if ($token->value === "(") {
            $next = $lexer->next();
            if ($next->type == Token::IDENTIFIER) {
                $this->paramNames[] = $next->value;
                $token = $lexer->next();

                while((',' === $token->value)) {
                    $this->paramNames[] = $this->matchIdentifier($lexer)->value;
                    $token = $lexer->next();
                }
            } else if ($next->type == Token::RBRACK) {
                return;
            } else {
                throw new Exception($this->errorInfo(). ' Expected was Identifier or ), but found: ' . $token->value);
            }
        }

        return;
    }

    public function next($basic) {

        if (! $basic->getScope()->has('__started')) {

            return $this->parent->next($basic);
        }

        $return = parent::next($basic);
        if ($this->isEnd($basic)) {

            $this->terminateFn($basic);
            if ($this->parent) {

                return $this->parent->next($basic);
            }

            // no statement to return in an expression
            return null;
        }

        return $return;
    }

    public function terminateFn($basic) {
        parent::terminate($basic);
        if ($basic->getScope()->has('__returnVar')) {
            $this->returnVar = $basic->getVar('__returnVar');
        }

        // restore parent from scope and not from property (scope is restored after processing INPUT)
        if ($basic->getScope()->has('__returnAddress')) {
            $this->parent = $basic->getVar('__returnAddress');
        }

        $this->removeScope($basic);
        if ($this->returnValue) {

            $basic->setVar($this->returnVar, $this->returnValue);
        }
    }

    /**
     * When entering a sub procedure, we have to set up a new scope.
     * @param $arguments
     * @param $basic
     */
    protected function addScope($arguments, $basic) {
        $basic->getScope()->push();
        for ($i = 0; $i < count($arguments); $i++) {
            $basic->setVar($this->paramNames[$i],$arguments[$i]);
        }
        if (!$this->returnVar) {
            $this->returnVar = '00_returnVar';
        }
        // put it in scope to be persisted during input.
        $basic->setVar('__returnVar', $this->returnVar);
        $basic->setVar('__returnAddress', $this->parent); // store return block in scope.
    }

    protected function removeScope($basic) {

        $basic->getScope()->pop();

    }
}