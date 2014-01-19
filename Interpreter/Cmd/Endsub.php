<?php
namespace PBasic\Interpreter\Cmd;

use PBasic\Interpreter\Cmd\AbstractStatement;
use PBasic\Interpreter\Parser;

namespace PBasic\Interpreter\Cmd;

class Endsub extends AbstractStatement
{
    public function execute($basic)
    {
        // do nothing
        //TODO: clean up scope should moved here.
    }

    public function parse(Parser $lexer, $basic)
    {
        // nothing to parse but ENDSUB.
    }
}
