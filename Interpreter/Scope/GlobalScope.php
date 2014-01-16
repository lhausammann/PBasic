<?php
namespace PBasic\Interpreter\Scope;

class GlobalScope extends NestedScope
{
    protected $globals = null;

    public function __construct()
    {
        $this->globals = new Scope();
        parent::__construct();
    }

    public function setGlobalVar($name, $value)
    {
        $this->globals[$name] = $value;
    }

    public function getGlobalVar($name, $value)
    {
        return $this->globals->get($name);
    }

    public function has($name)
    {
        return parent::has($name) || $this->globals->has($name);
    }

    public function resolve($name)
    {
        return
            ($this->globals->has($name) ?
                $this->globals->resolve($name) :
                parent::resolve($name));
    }
}
