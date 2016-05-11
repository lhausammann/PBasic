<?php
namespace PBasic\Interpreter\Cmd;
use PBasic\Interpreter\Cmd\AbstractStatement;
use PBasic\Interpreter\Expression\Token;
use PBasic\Interpreter\Parser;
use PBasic\Interpreter\Exception\ParserExecption;

class Data extends AbstractStatement
{
    private $expr = array();
    private $spaceOrTab = array();

    public function execute($basic)
    {
        // do nothing.
    }

    public function parse(Parser $parser, $basic)
    {
        $has = $basic->hasVar('00_DATA');
        $data = $has ? $basic->getVar('00_DATA') : array();
        $token = $parser->getLexer()->next();
        if ($this->isEol($token)) {
            throw new ParserExecption("At least one data entry must be given!");
        }
        $parser->getLexer()->setNext($token); // put token back
        $data[] = $parser->matchExpression();
        // DATA a,b,c, ...
        $lexer = $parser->getLexer();
        while ($token = $lexer->next()) {
            if ($this->isEol($token)) {
                break;
            }

            if ($token->value == ',') {
                $data[] = $parser->matchExpression();
            }
        }

        $basic->getScope()->setGlobalVar('00_DATA', $data);
        $basic->getScope()->setGlobalVar("00_DATA_IDX", 0);

    }
}
