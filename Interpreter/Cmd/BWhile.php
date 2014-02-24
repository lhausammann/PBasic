<?php
namespace PBasic\Interpreter\Cmd;

use PBasic\Interpreter\Cmd\AbstractStatement;
use PBasic\Interpreter\Expression\Token;
use PBasic\Interpreter\Parser;

class BWhile extends AbstractBlockStatement
{
    protected $statements = array();
    protected $isLoop = true;
    private $exprTree = null;
    private $block = null;

    public function parse(Parser $parser, $basic)
    {
        $this->exprTree = $parser->matchExpression();
        $this->block = $this->statements = $parser->parseUntil("WEND", $this);
        $this->matchEol($parser);
        ;
    }

    public function endBlock($stat)
    {
        $this->assertClass("WEND", $stat);

    }

    public function execute($basic)
    {
        return;

        $basic->setBreak(false);
        while (($basic->evaluateExpression($this->exprTree)) && ($basic->canContinue())) {
            $basic->runBlock($this->block);
        }
        $basic->setBreak(false);
    }

    public function next($basic)
    {
        if ($this->getInstructionPointer($basic) == 0) {
            if ($basic->evaluateExpression($this->exprTree)) {
                $next = parent::next($basic);

                return $next;
            }
            // quit block
            $this->terminate($basic);

            return $this->parent->next($basic);
        } else {
            return parent::next($basic);
        }
    }
}
