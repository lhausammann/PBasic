<?php
namespace PBasic\Interpreter\Cmd;
use PBasic\Interpreter\Cmd\AbstractStatement;
use PBasic\Interpreter\Expression\Token;
use PBasic\Interpreter\Parser;

class BPrint extends AbstractStatement
{
    private $expr = array();
    private $spaceOrTab = array();

    public function execute($basic)
    {
        $msg = '';
        $s = $this->spaceOrTab;
        foreach ($this->expr as $e) {
            $msg .= $basic->evaluateExpression($e);
            $sep = array_shift($s);
            if ($sep == ',') {
                $msg .= "\t";
            } else {
                $msg .= " ";
            }
        }

        echo '<div class="print" style="color:' . $basic->getForegroundColor() . ';background-color:' . $basic->getBackgroundColor() . ';display:block;width:100%">';
        echo '' . $msg . '</div>';
    }

    public function parse(Parser $parser, $basic)
    {
        $token = $parser->getLexer()->next();
        if ($this->isEol($token)) {
            return;
        }
        $parser->getLexer()->setNext($token); // put token back
        $this->expr[] = $parser->matchExpression();
        // PRINT a,b,c, ...
        $lexer = $parser->getLexer();
        while ($token = $lexer->next()) {
            if ($this->isEol($token)) {
                break;
            }

            if ($token->value == ',' || $token->value == ';') {
                $this->expr[] = $parser->matchExpression();
                $this->spaceOrTab[] = $token->value;
            }
        }
    }
}
