<?php
class BIf extends AbstractBlockStatement
{
    private $exprTree = null;
    private $ifBlock = null;
    private $elseBlock = null;
    private $name = "";
    private $hasElse;

    public function parse(Parser $parser, $basic)
    {
        $lexer = $parser->getLexer();
        $this->exprTree = $parser->matchExpression($lexer);
        $this->match("THEN", $lexer);

        $parser->parseUntil("ENDIF", $this);

        $this->matchEol($lexer);
    }

    public function statementParsed($stat)
    {
        if ($stat->getName()==="ELSE") {
            $this->hasElse = true;
        }
        if (! $this->hasElse) {
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
        throw new Exception("Executing block should not happen");

        return;
        if ($basic->evaluateExpression($this->exprTree)) {
            $basic->runBlock($this->ifBlock);
        } else {
            if ($this->elseBlock) {
                $basic->runBlock($this->elseBlock);
            }
        }
    }

    public function next($basic)
    {
        $next = $this->parent;
        if ($basic->evaluateExpression($this->exprTree)) {
            $next = $this->statements[0]->next($basic);

            return $next;
        } elseif ($this->elseBlock) {
            return $this->statements[1]->next($basic);
        } else {
            return $this->parent->next($basic);
        }
    }

}
