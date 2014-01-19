<?php
namespace PBasic\Interpreter\Expression;


class ExpressionVisitor
{
    public static $parser;

    public function __construct($parser)
    {
        $this->setParser($parser); // for scope and callbacks
    }

    public function visit($node)
    {
        return self::visitStatic($node);
    }

    // for speed: dispatch direclty instead of calling back to node->accept
    public static function visitStatic($node)
    {
        switch ($node->token->type) {
            // scalar
            case Token::IDENTIFIER : return self::visitIdentifier($node);
            case Token::NUMBER : return $node->token->value;
            case Token::STRING : return $node->token->value;
            // composites
            case Token::FUNC : return self::visitFunction($node);
            case Token::OPERATOR : return self::visitOperator($node);
            case Token::UNARY_MINUS : return self::visitOperatorUnaryMinus($node);
            case Token::UNARY_NOT : return self::visitOperatorUnaryNot($node);

            return self::visitUnknown($node);
        }
    }

    public function setParser($parser)
    {
        self::$parser = $parser;
    }

    public function visitRbrack($node)
    {
        throw new Exception ('should not happen visiting token:' . $node);
    }

    public function visitLBrack($node)
    {
        throw new Exception('should not happen visiting token:' . $node);
    }

    public static function visitNumber($node)
    {
        return $node->token->value;
    }

    public static function visitOperator($node)
    {
        list($left, $right) = $node->getChildren();

        return self::compute($node, $left, $right);
    }

    public static function visitIdentifier($node)
    {
        return self::$parser->resolve($node->getValue());

    }

    public static function visitString($node)
    {
        return $node->getValue();
    }

    public static function visitFunction($node)
    {
        $args = $node->getChildren();
        $evaluatedArgs = array();
        foreach ($args as $arg) {
            $evaluatedArgs[] = self::visitStatic($arg);
        }
        $fn = $node->getValue();

        return self::$parser->executeFunction($fn, $evaluatedArgs);
    }

    public function visitSemicolon($node)
    {
        throw new Exception('should not happen visiting semicolon.');
    }

    public static function visitOperatorUnaryMinus($node)
    {
        $next = $node->getSingleChild();

        return - (self::visitStatic($next));
    }

    public static function visitOperatorUnaryNot($node)
    {
        $next = $node->getSingleChild();

        return ! (self::visitStatic($next));
    }

    public static function visitStructure($node)
    {
        // resolve the scope string:
        return self::$parser->resolveScope($node->getValue());
    }

    private static function compute($op, $leftNode, $rightNode)
    {
        $left = self::visitStatic($leftNode);
        $right = self::visitStatic($rightNode);
        $operation = $op->getValue();
        $isString = ! is_numeric($left) || ! is_numeric($right);

        switch ($operation) {
            case '+' :
                return $isString ? $left . $right :
                 $left + $right;
            case '-' : return $left - $right;
            case '*' : return $left * $right;
            case '/' : return $left / $right;

            case 'AND' : return $left && $right;
            case 'OR' : return $left || $right;

            case '=' : return $left == $right;
            case '!=': return $left != $right;
            case '<' : return $left < $right;
            case '<=': return $left <= $right;
            case '>' : return $left > $right;
            case '>=': return $left >= $right;

            default :
                $function = strtolower($operation);
                // is there a function in the visitor to call?
                if (method_exists(self,$function)) {
                    return call_user_func(array(self, $function), $left, $right);
                } // is it an Operator defined in parser?

                if (method_exists(self::parser,$function)) {
                    return call_user_func(array(self::parser, $function), $left, $right);
                }

                throw new Exception ('Could not find operator:' . $op);
        }
    }
}
