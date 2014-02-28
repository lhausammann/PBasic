<?php

namespace PBasic\Interpreter\Cmd;
use PBasic\Interpreter\Parser;
use PBasic\Interpreter\Cmd\Block;
class Gosub extends AbstractStatement
{
    private $label;
    private $returnAddress;
    private $block;

    public function parse(Parser $parser, $basic)
    {
        $block = $this->block =         $this->block = new Block('block',0,0,0,0);

        $this->label = $this->matchNumber($parser->getLexer())->value;
        $goto = new BGoto(0,0,0,0);
        $goto->setLabel($this->label);
        $goto->setParent($block);
        $this->returnLabel = -(rand(1000, 1000000));
        $this->matchEol($parser->getLexer());

        $label = new Label($this->returnLabel);
        $label->setParent($block);
        $basic->notify($label);

        $this->block->setParent($this);
        $this->block->addChild($goto);
        $this->block->addChild($label);
    }

    public function execute($basic)
    {
        $basic->addReturn($this);
    }

    public function next($basic)
    {
        $next = $this->block->next($basic);
        return $next;

    }

    public function terminate() {}
}
