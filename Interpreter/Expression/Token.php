<?php

namespace PBasic\Interpreter\Expression;

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

