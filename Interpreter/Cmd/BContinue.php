<?php
namespace PBasic\Interpreter\Cmd;
use PBasic\Interpreter\Cmd\AbstractStatement;
use PBasic\Interpreter\Expression\Token;
use PBasic\Interpreter\Parser;

class BContinue extends AbstractStatement
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
     * @param AbstractStatement next statement to execute.
     */
    public function next($basic)
    {
        // find the next look to break
        $p = $this->parent;
        while ($p && $p->isLoop() == false) {
            $p = $p->parent;

        }

        $this->parent->terminate($basic); // jump to end of parent and

        return $p->next($basic);

        throw new \Exception ("Can only continue loops.");

    }
}
