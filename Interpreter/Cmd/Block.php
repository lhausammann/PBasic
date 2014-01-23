<?php
namespace PBasic\Interpreter\Cmd;

use PBasic\Interpreter\Parser;
use PBasic\Interpreter\Expression\Token;
/**
 * A container which contains statements.
 * Next() will call the grand parent instead of the parent statement
 * to skip the currently executing statement.
 * @author luz
 *
 */
class Block extends AbstractBlockStatement
{
    public function parse(Parser $parser, $basic)
    {
    }

    public function execute($basic)
    {
    }

    public function next($basic)
    {
        if ($this->canContinue($basic)) {
            return parent::next($basic);
        }

        // skip parent block, which will return
        // the first statement of this block
        // again.
        $this->terminate($basic); // leave this block and

        $this->parent->terminate($basic); // leave else block

        $next =  $this->parent->parent->next($basic);

        return $next;
    }
}
