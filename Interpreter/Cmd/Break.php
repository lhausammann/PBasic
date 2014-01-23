<?php
namespace PBasic\Interpreter\Cmd;
use PBasic\Interpreter\Cmd\AbstractStatement;
use PBasic\Interpreter\Expression\Token;
use PBasic\Interpreter\Parser;
class BBreak extends AbstractStatement
{
    public function execute($basic)
    {
    }

    public function parse(Parser $lexer, $basic)
    {
        // no arguments to parse here.
        return;
    }

    /**
     * Find the next loop to break.
     * @param $basic basic instance to resolve vars
     * @return AbstractStatement | null the next statement to continue with
     */
    public function next($basic)
    {
        $p = $this->parent;
        while ($p && $p->isLoop() == false) {
            $p = $p->parent;
        }

        $this->parent->terminate($basic); // clean up parent block and
        if ($p->parent) {
            return $p->parent->next($basic); // exit this loop
        }

        throw new \Exception ("Can only break loops.");

    }
}
