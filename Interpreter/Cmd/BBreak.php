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
        // find the first loop block to break
        while ($p && $p->isLoop() == false) {
            $p = $p->parent;
        }

        $this->parent->terminate($basic); // quit the parent block immediately (could be an IF Block)
        if ($p->parent) { // do not quit the outmost block.
            return $block = $p->terminate($basic); // quit the loop.
            return $p->parent->next($basic); // return the next statement after the loop.
        }

        throw new \Exception ("Can only break loops.");

    }
}
