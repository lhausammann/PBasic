<?php
namespace PBasic\Interpreter\Cmd;

use PBasic\Interpreter\Parser;
use PBasic\Interpreter\Cmd\AbstractStatement;
use PBasic\Interpreter\Expression\Token;

class BEndif extends AbstractStatement
{
    public function execute($basic)
    {
        // do nothing
    }

    public function parse(Parser $lexer, $basic)
    {
        // do nothing
    }

}
