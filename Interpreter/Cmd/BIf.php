<?php

namespace PBasic\Interpreter\Cmd;
use PBasic\Interpreter\Cmd\AbstractStatement;
use PBasic\Interpreter\Expression\Token;
use PBasic\Interpreter\Parser;

class BIf extends AbstractBlockStatement
{
    private $exprTree = null;
    private $ifBlock = null;
    private $elseBlock = null;
    private $hasElse;

    public function parse(Parser $parser, $basic)
    {

        $lexer = $parser->getLexer();
        $this->exprTree = $parser->matchExpression($lexer);
        $this->match("THEN", $lexer);
        // IF A = 10 THEN <label>
        if ($this->tryMatchNumber($lexer)) {
            $nr = $this->matchNumber($lexer)->value;

            $jump = new BGoto('goto ' . $nr, $this->instrNr, 0, $this->blockNr);
            $jump->setLabel($nr);
            $this->ifBlock = array($jump);
            $this->endBlock(null);
        } else {
            // match whole block
            $parser->parseUntil("ENDIF", $this);
            $this->matchEol($lexer);
        }

    }

    public function statementParsed($stat)
    {
        if ($stat->getName() === "ELSE") {
            $this->hasElse = true;
        }
        if (!$this->hasElse) {
            $this->ifBlock[] = $stat;
        } else {
            $this->elseBlock[] = $stat;
        }

    }

    public function endBlock($statement)
    {
        // remove added children and pack them into if / else blocks:
        $this->statements = array();
        $if = new Block('ifblock', 0, 0, 0);
        $if->setParent($this);
        $else = new Block('elseblock', 0, 0, 0);
        $else->setParent($this);

        foreach ($this->ifBlock as $stat) {
            $if->addChild($stat);
        }
        if ($this->hasElse) {
            foreach ($this->elseBlock as $stat) {
                $else->addChild($stat);
            }
        }
        $this->statements[0] = $if;
        $this->statements[1] = $else;

    }

    public function execute($basic)
    {
    }

    public function next($basic)
    {
        if ($basic->evaluateExpression($this->exprTree)) {
            $next = $this->statements[0]->next($basic);
            return $next;
        } elseif ($this->elseBlock) {
            return $this->statements[1]->next($basic);
        } else {
            return $this->parent;
        }
    }

}
