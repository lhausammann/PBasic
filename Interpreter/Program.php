<?php
namespace PBasic\Interpreter;

use PBasic\Interpreter\Cmd\AbstractBlockStatement;

class Program extends AbstractBlockStatement
{
    private $gotoTable = array();

    public function parse(Parser $parser, $basic)
    {
        $parser->addObserver($this);
        $parser->parseUntil('', $this);
        $parser->removeObserver($this);
        return $this;
    }

    public function update($stat)
    {
        if ($stat->getName() == 'LABEL') {
            $this->gotoTable[$stat->getLabel()] = $stat;
        }
    }

    public function execute($basic)
    {
    }

    public function jump($label, $basic)
    {
        $stat = $this->gotoTable[$label];
        return $stat->setAsCurrent($basic, $stat);
    }
}
