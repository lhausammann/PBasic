<?php
class Let extends AbstractStatement
{
    private $exprTree = null;
    private $exprParser;
    private $name = "";

    public function parse(Parser $parser, $basic)
    {
        $lexer = $parser->getLexer();
        $this->name = $this->matchIdentifier($lexer)->value;
        $this->matchEqualSign ($lexer);
        $this->exprTree = $parser->matchExpression();
        $this->matchEol($lexer);
    }

    public function execute($basic)
    {
        // store it in global scope

        $value = $basic->evaluateExpression($this->exprTree);
        $basic->setVar($this->name, $value);

    }

    public function matchEqualSign($lexer)
    {
        return $this->match("=", $lexer);
    }
}
