<?php
namespace PBasic\Interpreter\Cmd;

use PBasic\Interpreter\Cmd\AbstractStatement;
use PBasic\Interpreter\Expression\Token;
use PBasic\Interpreter\Parser;

class BReturn extends AbstractStatement
{
    private $exprTree = null;
    private $fn = null;

    public function parse(Parser $parser, $basic)
    {
        if ($token = $parser->next()) {
            if ($token->type == Token::DOUBLE_POINT) {
                $this->exprTree = null;
            } else {
                $parser->getLexer()->setNext($token);
                $this->exprTree = $parser->matchExpression();
            }
        }
    }

    public function execute($basic)
    {
        if ($this->fn) {
            $this->fn->forceEnd($basic); // jump to endsub statement
            $return = $basic->evaluateExpression($this->exprTree);
            $this->fn->setReturnValue($return, $basic);
        }
    }

    public function setFunction($fn)
    {
        $this->fn = $fn;
    }

    public function next($basic)
    {
        if (! $this->fn) {
            // retrieve the return value from basic, because its a gosub call
            $returnStat = $basic->getReturn();
            //$this->parent->statements = $returnStat->parent->statements; // HACK to allow finding return address.
            $returnStat->isReturning = true; // indicate we are returning.
            $this->setAsCurrent($basic, $returnStat, $returnStat->parent->statements); /* set it as statement executing this cycle, so  calling next on parent will return the following statement.
            //TODO: check how it works exactly. */
            return $returnStat;
        }

        $this->fn->forceEnd($basic);
        $next = $this->fn->next($basic);
        return $next;
    }
}
