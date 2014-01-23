<?php

namespace PBasic\Interpreter\Cmd;

use PBasic\Interpreter\Cmd\AbstractStatement;
use PBasic\Interpreter\Expression\Token;
use PBasic\Interpreter\Parser;

class Color extends AbstractStatement
{
    private $fgTree = null;
    private $bgTree = null;

    public function parse(Parser $parser, $basic)
    {
        $lexer = $parser->getLexer();
        $this->fgTree = $parser->matchExpression();
        // try to get next token
        $token = $lexer->next();
        if ($token->value !== ',') {
            $lexer->setNext($token); // put it back

            return;
        }
        $this->bgTree = $parser->matchExpression();

    }

    public function execute($basic)
    {
        // store colors global
        $basic->setForegroundColor($basic->evaluateExpression($this->fgTree));
        if ($this->bgTree) {
            $basic->setBackgroundColor($basic->evaluateExpression($this->bgTree));
        }

    }
}
