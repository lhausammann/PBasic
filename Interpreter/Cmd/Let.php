<?php

namespace PBasic\Interpreter\Cmd;

use PBasic\Interpreter\Cmd\AbstractStatement;
use PBasic\Interpreter\Expression\Token;
use PBasic\Interpreter\Parser;

class Let extends AbstractStatement
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
        $this->matchEqualSign($lexer);
        $this->exprTree = $parser->matchExpression();
        $this->matchEol($lexer);
    }

    public function execute($basic)
    {
        // store it in global scope

        $value = $basic->evaluateExpression($this->exprTree);
        if ($this->arrIndex) {
            $arr = $basic->getVar($this->name);
            $arr[$basic->evaluateExpression($this->arrIndex)] = $value;
            $value = $arr; 
        }
        $basic->setVar($this->name, $value);

    }

    public function matchEqualSign($lexer)
    {
        return $this->match("=", $lexer);
    }
}
