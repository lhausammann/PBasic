<?php
namespace PBasic\Interpreter\Cmd;

use PBasic\Interpreter\Cmd\AbstractStatement;
use PBasic\Interpreter\Parser;

/**
 * Note: This goto implementation, as it stands currently, will allow you to jump out
 * of blocks. But never into a block.
 * @author luz
 *
 */
class BGoto extends AbstractStatement
{
    private $label;

    public function parse(Parser $parser, $basic)
    {
        $lexer = $parser->getLexer();
        $this->label = $this->matchNumber($lexer)->value;
    }

    public function execute($basic)
    {
        // do nothing - jump is handled in next.
    }

    public function next($basic)
    {
        // leave all blocks
        $this->parent->terminateAll($basic);
        $root = $this->parent;
        while ($root->parent) {
            $root = $root->parent;
        }

        return $root->jump($this->label, $basic);
    }
}
