<?php
namespace PBasic\Interpreter\Scope;


class Scope
{
    private $scope = array(
        "true" => true,
        "false" => false

    );

    public function current()
    {
        return $this;
    }

    public function getVar($name)
    {
        if ($this->has($name)) {
            $scope = $this->current()->scope;

            return $scope[$name];
        }

        throw new \Exception("Var " . $name . ' not found in scope.');
    }

    public function setVar($name, $value)
    {
        $scope = & $this->current()->scope;
        $scope[$name] = $value;
    }

    public function has($name)
    {
        $scope = $this->current()->scope;

        return array_key_exists($name, $scope);
    }

    public function resolve($name)
    {
        return $this->current()->getVar($name);
    }
}
