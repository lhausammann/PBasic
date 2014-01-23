<?php
namespace PBasic\Interpreter\Cmd;

use PBasic\Interpreter\Cmd\AbstractStatement;
use PBasic\Interpreter\Expression\Token;
use PBasic\Interpreter\Parser;

class End extends AbstractStatement
{
    public function parse(Parser $parser, $basic)
    {
        // nothing to do
    }

    public function execute($basic)
    {
        exit;

    }

    public function next($basic)
    {
        $this->parent->terminateAll($basic); // shutdown all open blocks
        // end of statement stream reached.
        return null;
    }
}
