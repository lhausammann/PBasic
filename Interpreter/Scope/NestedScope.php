<?php
namespace PBasic\Interpreter\Scope;

use PBasic\Interpreter\Scope\Scope;

class NestedScope extends Scope
{
    private $scopes = array();

    public function __construct()
    {
        $this->scopes[] = new Scope();
    }

    public function depth()
    {
        return count($this->scopes);
    }

    public function current()
    {
        return $this->scopes[count($this->scopes) - 1];
    }

    public function pop()
    {
        array_pop($this->scopes);
    }

    public function push()
    {
        $this->scopes[] = new Scope();
    }
}