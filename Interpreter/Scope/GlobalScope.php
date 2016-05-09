<?php
namespace PBasic\Interpreter\Scope;

/** 
 * GlobalScope allows nested scopes with one global scope.
 */

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
        $this->globals->setVar($name, $value);
    }

    public function getGlobalVar($name, $throws = true)
    {
        try {
            return $this->globals->resolve($name);
        } catch(\Exception $e) {
            if ($throws) {
                throw $e;
            } 
        }

        return null;
    }

    public function has($name)
    {
        try {
            return parent::has($name); 
        } catch (\Exception $e) {
            return $this->globals->has($name);
        }
    }

    public function resolve($name)
    {
        // TODO: must global really take predecence always?
        return
            ($this->globals->has($name) ?
                $this->globals->resolve($name) :
                parent::resolve($name));
    }
}
