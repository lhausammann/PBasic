<?php

namespace PBasic\Interpreter\Cmd;

use PBasic\Interpreter\Cmd\AbstractStatement;
use PBasic\Interpreter\Expression\Token;
use PBasic\Interpreter\Parser;

class Dim extends AbstractStatement
{
    private $exprTree = null;
    private $exprParser;
    private $arrIndex = null;

    private $name = "";

    public function parse(Parser $parser, $basic)
    {
        $lexer = $parser->getLexer();
        $this->name = $this->matchIdentifier($lexer)->value;
        // is it an array?
        if ($this->tryMatch("(", $lexer, true)) {

            $this->arrIndex = $parser->matchExpression($lexer);
            $this->match(")", $lexer);
        } 
        $this->matchEol($lexer);
    }

    public function execute($basic)
    {
        // store it in global scope

        $value = $basic->evaluateExpression($this->exprTree);
        if ($this->arrIndex) {
            $array = array();
            $idx = $basic->evaluateExpression($this->arrIndex);
            $array = array_fill(0, $idx -1, 5);
            $basic->setVar($this->name, $array);
        }
        $basic->setVar($this->name, $value);

    }

    public function matchEqualSign($lexer)
    {
        return $this->match("=", $lexer);
    }
}
