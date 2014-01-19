<?php
namespace PBasic\Interpreter\Expression\Ast;

use PBasic\Interpreter\Expression\Ast\Ast;


class AstLeave
{
    public $token;
    public $nodeType = Ast::LEAVE;

    public function __construct($token)
    {
        $this->token = $token;
        $this->nodeType = Ast::LEAVE;
    }

    public function getValue()
    {
        return $this->token->value;
    }

    public function getNodeType()
    {
        return $this->nodeType;
    }

    public function getType()
    {
        return $this->token->type;
    }

    //TODO: use Token->getNamedType instead of getType().
    public function getNamedType()
    {
        return $this->token->getType();
    }

    public function accept($visitor)
    {
        return $visitor->visit($this);
    }

    public function __toString()
    {
        return "" . $this->token->value;
    }
}