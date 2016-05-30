<?php
namespace PBasic\Interpreter\Cmd;

use PBasic\Interpreter\Cmd\AbstractStatement;
use PBasic\Interpreter\Parser;
use PBasic\Interpreter\Expression\Token;

class BFor extends AbstractBlockStatement
{
    private $exprTreeInit = null;
    private $exprTreeTo = null;
    private $exprTreeStep = null;
    private $block = null;
    private $name = null;

    public function isLoop()
    {
        return true;
    }

    public function parse(Parser $parser, $basic)
    {
        $lexer = $parser->getLexer();
        $this->name = $this->matchIdentifier($lexer)->value;
        $this->match('=', $lexer);
        $this->exprTreeInit = $parser->matchExpression($lexer);
        $this->match("TO", $lexer);
        $this->exprTreeTo = $parser->matchExpression($lexer);
        $token = $lexer->next();
        if ($token->type != Token::DOUBLE_POINT) {
            if (strtoupper($token->value) == "STEP") {
                $this->exprTreeStep = $parser->matchExpression($lexer);
            } else {
                throw new Exception("Expected was STEP but found: " . $token . $this->errorInfo());
            }
        } else {
            $lexer->setNext($token); // put it back
        }
        $this->matchEol($lexer);

        $block = $parser->parseUntil("NEXT", $this);
        $this->block = $this->statements = $block;
        $this->matchEol($lexer);
    }

    public function endBlock($next)
    {
        $this->assertClass("NEXT", $next);
        $next->setParent($this);
        // check running var
        if ($i = $next->getVar()) {
            if ($this->name != $i) {
                throw new Exception("Running var in for not matching with NEXT. " . $this->errorInfo());
            }
        }
    }

    public function execute($basic)
    {
        // do nothing
    }

    public function next($basic)
    {

        // from, to, step can be changed inside the for loop.
        // evaluate them each time.
        $from = ($basic->evaluateExpression($this->exprTreeInit));
        $to = $basic->evaluateExpression($this->exprTreeTo);
        $step = $this->exprTreeStep ? $basic->evaluateExpression($this->exprTreeStep) : 1;
        $this->current = $this->getInstructionPointer($basic);
        //echo $this;
        //var_dump($this->current);
        if ($this->current > 0) {
            $i = $basic->getVar($this->name);
        } else {
            $i = $from;
            if ($this->isInRange($i, $to, $step, $from)) {
                $basic->setVar($this->name, $i);
                $stat = parent::next($basic);

                return $stat;
            }
        }

        // init a new loop
        if (count($this->statements) - 1 <= $this->current) {
            $this->current = 0;
            $this->setInstructionPointer($this->current, $basic);

            $i += $step;
            if ($this->isInRange($i, $to, $step, $from)) {
                $basic->setVar($this->name, $i);
                return parent::next($basic);
            } else {

                $this->terminate($basic);
                //return $this->parent;

                return $this->parent->next($basic);
            }
        }


        return parent::next($basic);
        $stat = $this->statements[$this->current];
        $this->current++;
        $this->setInstructionPointer($this->current, $basic);

        return $stat;
    }

    private function isInRange($i, $to, $step, $from)
    {
        if ((($to - $from) < 0) && ($step < 0)) {
            return $i >= $to;
        }
        return $i <= $to;
    }
}

