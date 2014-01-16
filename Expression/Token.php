<?php
/**
 * These classes are used by the ExpressionParser/ExpressionVisitor to build
 * expressions
 */

class Token
{
    const RBRACK = 1;
    const LBRACK = 2;
    const NUMBER = 3;
    const OPERATOR = 4;
    const IDENTIFIER = 5;
    const STRING = 6;
    const UNARY_MINUS = 7; // needed for parser.
    const UNARY_NOT = 8;
    const FUNC = 9;
    const SEMICOLON = 10;
    const PERIOD = 11;
    const STRUCTURE = 12;
    const QUESTION = 13;
    const END = 0;
    const DOUBLE_POINT = 14;
    const HIGH_COMMATA = 15;

    public $type;
    public $value;

    public function getType()
    {
        switch ($this->type) {
            // tokens which the lexer generates
            case self::RBRACK: return 'RBRACK';
            case self::LBRACK: return 'LBRACK';
            case self::NUMBER: return 'NUMBER';
            case self::OPERATOR: return 'OPERATOR';
            case self::IDENTIFIER: return 'IDENTIFIER';
            case self::STRING: return 'STRING';
            case self::FUNC: return 'FUNCTION';
            case self::SEMICOLON: return 'SEMICOLON';
            case self::PERIOD: return 'PERIOD';
            case self::QUESTION: return 'QUESTION_MARK';
            case self::DOUBLE_POINT: return 'DOUBLEPOINT';
            case self::DOUBLE_POINT: return 'HIGHCOMMATA';
            case self::END: return 'END';

            case self::UNARY_MINUS: return 'OPERATOR_UNARY_MINUS';
            case self::UNARY_NOT: return 'OPERATOR_UNARY_NOT';
            case self::STRUCTURE: return 'STRUCTURE';
            default : return 'UNKNOWN';
        }
    }

    public function __construct($type, $value)
    {
        $this->type = $type;
        $this->value = $value;
    }

    public function __toString()
    {
        return $this->getType() . ': "' . $this->value . '"<br />';
    }
}

class Ast
{
    const LEAVE = 0;
    const NODE = 1;
    static $nodeTypes = array(
        LEAVE => 'LEAVE',
        NODE => 'NODE',
    );

}

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
        /*
        $fn = 'visit' . ucfirst(strtolower($this->getNamedType()));
        // dispatch

        if (strpos($fn, '_')!==false) {
            $parts = explode('_', $fn);
            $fn = '';
            foreach ($parts as $part) {
                $fn .= ucfirst($part);
            }
        }
        $fn = 'visit' . $fn;
        $visitor->$fn;
        */

        // better dispatch
        // directly on the visitor for speed
        return $visitor->visit($this);
    }

    public function __toString()
    {
        return $this->token->value;
    }
}

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
            throw new Exception('Assuming single child, but there are more: ' . $this);
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
            $children.= $sep . $child->__toString();
            $sep = ', ';
        }
        $ret = $children ? $ret . '(' . $children . ')' : $ret;

        return $ret;
    }
}
