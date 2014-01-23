<?php
namespace PBasic\Interpreter\Cmd;
use PBasic\Interpreter\Cmd\AbstractStatement;
use PBasic\Interpreter\Parser;

class Scopedump extends AbstractStatement
{
    public function parse(Parser $parser, $basic)
    {
        // nothing to do
        exit;
        $this->matchEol($parser->getLexer());
    }

    public function execute($basic)
    {
        echo "Break all set: " . $basic->breakAll();
        echo "Break set: " . $basic->isBreak();
        $basic->dumpScope();
    }
}
