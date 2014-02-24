<?php
namespace PBasic\Interpreter\Cmd;

use PBasic\Interpreter\Cmd\AbstractStatement;
use PBasic\Interpreter\Expression\Token;
use PBasic\Interpreter\Parser;

class Endsub extends AbstractStatement
{
    public function parse(Parser $parser, $basic)
    {
        // nothing to do
    }

    public function execute($basic)
    {
        // nothing to do
    }

    public function next($basic)
    {
        //throw new Exception("Reaching next");
    }


}
