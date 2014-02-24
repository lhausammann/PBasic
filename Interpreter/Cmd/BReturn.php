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

        $this->fn->forceEnd($basic); // jump to endsub statement
        $return = $basic->evaluateExpression($this->exprTree);
        $this->fn->setReturnValue($return, $basic);
    }

    public function setFunction($fn)
    {
        $this->fn = $fn;
    }

    public function next($basic)
    {

        $this->fn->forceEnd($basic);
        $next = $this->fn->next($basic);
        return $next;
    }
}
