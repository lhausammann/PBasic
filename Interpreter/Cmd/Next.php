<?php
namespace PBasic\Interpreter\Cmd;

use PBasic\Interpreter\Cmd\AbstractStatement;
use PBasic\Interpreter\Parser;

// while <expr> <list of statements>* wend
class Next extends AbstractStatement
{
    private $var = null;

    public function execute($basic)
    {
        // do nothing
    }

    public function parse(Parser $parser, $basic)
    {
        $lexer = $parser->getLexer();
        $possibleVar = $lexer->next();
        if ($possibleVar->type === Token::IDENTIFIER) {
            $this->var = $possibleVar->value;
            //$this->matchEol($lexer);
        } else {

            $lexer->setNext($possibleVar); // put it back to the lexer
        }

    }

    // parent (FOR) will check back if the var is the same as the running var, if given.
    public function getVar()
    {
        return $this->var;
    }
}
