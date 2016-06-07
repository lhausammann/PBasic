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
            echo "ret:" . $returnStat;
            $returnStat->forceEnd($basic);
            /*
             * Because GoSub works like any block statement (all child statements are actual program statements with a parent pointing to gosub) it must return the parent of gosub rather than gosub itself to prevent endless calls to gosub.
             */
            return $returnStat->parent;
        }

        $this->fn->forceEnd($basic);
        $next = $this->fn->next($basic);
        return $next;
    }
}
