<?php

namespace PBasic\Interpreter\Cmd;
use PBasic\Interpreter\Cmd\AbstractStatement;
use PBasic\Interpreter\Expression\Token;
use PBasic\Interpreter\Parser;

class Read extends AbstractStatement
{
    protected $input;
    protected $value = '';
    protected $message = '';

    public function parse(Parser $parser, $basic)
    {

        $this->input = $this->matchIdentifier($parser->getLexer());

    }

    public function execute($basic)
    {
        $name = $this->input->value;
        $data = $basic->getVar("00_DATA");
        $idx = $basic->getVar("00_DATA_IDX");
        $basic->setVar("00_DATA_IDX", $idx + 1); // put it back.
        $basic->setVar($name, $data[$idx]);        
    }
}
