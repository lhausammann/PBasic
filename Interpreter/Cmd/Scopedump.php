<?php
namespace PBasic\Interpreter\Cmd;
use PBasic\Interpreter\Cmd\AbstractStatement;
use PBasic\Interpreter\Parser;

class Scopedump extends AbstractStatement
{
    public function parse(Parser $parser, $basic)
    {
        // nothing to do

        $this->matchEol($parser->getLexer());
    }

    public function execute($basic)
    {
        $basic->dumpScope();
    }
}
