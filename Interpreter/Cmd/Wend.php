<?php
namespace PBasic\Interpreter\Cmd;
use PBasic\Interpreter\Cmd\AbstractStatement;
use PBasic\Interpreter\Parser;

class Wend extends AbstractStatement
{
    public function execute($basic)
    {
        // do nothing
    }

    public function parse(Parser $lexer, $basic)
    {
        // do nothing
    }

    public function next($basic)
    {
        $this->parent->terminate($basic); // reset this block

        return $this->parent->next($basic);
    }

}
