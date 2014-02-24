<?php
namespace PBasic\Interpreter\Expression\Ast;

use PBasic\Interpreter\Expression\Ast\AstLeave;
use PBasic\Interpreter\Expression\Ast\Ast;

use PBasic\Interpreter\Expression\Token;

class AstNode extends AstLeave
{
    public $children = array();

    public function __construct(Token $token)
    {
        $this->token = $token;
        $this->nodeType = Ast::NODE;
    }

    public function getChildren()
    {
        return $this->children;
    }

    public function getFirstChild()
    {
        return $this->children[0];
    }

    public function getSingleChild()
    {
        if (count($this->children) > 1) {
            throw new \Exception('Assuming single child, but there are more: ' . $this);
        }

        return $this->children[0];
    }

    public function getSingleChildValue()
    {
        return $this->getSingleChild()->token->value;
    }

    public function getValue()
    {
        return $this->token->value;
    }

    public function addChild($child)
    {
        $this->children[] = $child;
    }

    public function __toString()
    {
        $ret = $this->token->getType() . ' [' . $this->token->value . ' ] ';
        $children = '';
        $sep = '';
        foreach ($this->children as $child) {
            $children .= $sep . $child->__toString();
            $sep = ', ';
        }
        $ret = $children ? $ret . '(' . $children . ')' : $ret;

        return $ret;
    }
}