<?php
/**
 * A parser for expressions.
 */
namespace PBasic\Interpreter\Expression;

use PBasic\Interpreter\Expression\Token;
use PBasic\Interpreter\Expression\Ast\AstNode;
use PBasic\Interpreter\Expression\Ast\AstLeave;
use PBasic\Interpreter\Lexer;

use PBasic\Interpreter\Expression\Exception\ExpressionException;
use PBasic\Interpreter\Expression\Exception\ExpressionParsingException;

class ExpressionParser
{
    protected $lookahead = null;
    private $input = '';
    private $buffer = array();

    public function __construct(Lexer $input)
    {
        $this->root = new AstNode(new Token('root', 'root'));
        $this->input = $input;
        $this->lookahead = $this->next();
    }

    public function start($expectEnd = true)
    {
        $result = $this->matchExpression();
        if ($this->isEnd()) {
            $this->lookahead = null;
            return $result;
        } else if ($expectEnd) {
            // normally caused by wrong nested parenthesis
            throw new ExpressionParsingException('Parse Error: Expected was end of file, but found token was:' . $this->lookahead);
        }
    }

    public function isEnd()
    {

        return $this->lookahead->type == (Token::END) && count($this->buffer == 0);
    }

    public function getLookahead()
    {
        return $this->lookahead;
    }

    public function matchExpression()
    {
        $current = $this->matchTermLogic();

        while (!$this->isEnd() && $this->isLookaheadOperatorLogic()) {
            $left = $current; // save
            $current = $this->matchOperatorLogic(); // descend to logical operators
            $current->addChild($left); // add save to left
            $current->addChild($this->matchTermLogic()); // add next to right
        }

        return $current;
    }

    protected function consume()
    {
        if (!$this->isEnd()) {
            $this->lookahead = $this->next();
        }
    }

    private function matchTermLogic()
    {
        $current = $this->matchTermAndOr();
        while (!$this->isEnd() && $this->isLookaheadOperatorAndOr()) {
            $left = $current; // save
            $current = $this->matchOperatorAndOr(); // descend
            $current->addChild($left); // add save to left
            $current->addChild($this->matchTermAndOr()); // add next to right
        }

        return $current;
    }

    private function matchTermAndOr()
    {
        $current = $this->matchTermPlusMinus();
        while (!$this->isEnd() && $this->isLookaheadOperatorPlusMinus()) {
            $left = $current; // save
            $current = $this->matchOperatorPlusMinus(); // descend
            $current->addChild($left); // add save to left
            $current->addChild($this->matchTermPlusMinus()); // add next to right
        }

        return $current;
    }


    private function matchTermPlusMinus()
    {
        $current = $this->matchTermMulDiv();
        while (!$this->isEnd() && $this->isLookaheadOperatorMulDiv()) {
            $left = $current;
            $current = $this->matchOperatorMulDiv();
            $right = $this->matchTermMulDiv();
            $current->addChild($left);
            $current->addChild($right);
        }

        return $current;
    }

    private function matchTermMulDiv()
    {
        if (($this->lookahead->type == TOKEN::NUMBER)) {
            $current = $this->match(Token::NUMBER);
        } elseif ($this->lookahead->type == Token::IDENTIFIER) {
            // is it a function call?
            if ($this->peek()->type == Token::LBRACK) {
                $current = $this->matchFunction();
            } elseif ($this->peek(0)->type == Token::PERIOD) {
                $current = $this->matchStruct();
            } else {
                $current = $this->match(Token::IDENTIFIER);
            }
        } elseif ($this->lookahead->type == Token::STRING) {
            $current = $this->match(Token::STRING);
        } elseif ($this->lookahead->type == Token::LBRACK) {
            $current = $this->matchParenthesizedExpression();
        } elseif ($this->lookahead->type == Token::OPERATOR) {
            $current = $this->matchUnaryOp();
        } else {
            throw new ExpressionParsingException('Could not match unary operator. Expected: Number, (, unary -, but found: ' . $this->lookahead);
        }

        return $current;

    }

    protected function match($match)
    {
        $token = $this->lookahead;
        if ($this->lookahead->type === $match) {
            $this->consume();
            return new AstLeave($token);
        }
        throw new ExpressionParsingException('Expected: ' . $match . ' but found: ' . $this->lookahead);
    }

    private function matchOperatorMulDiv()
    {
        $token = $this->lookahead;
        if (!$this->isOperatorMulDiv($this->lookahead->value)) {
            throw new ExpressionParsingException('Exception: Expected * or / but found ' . $this->lookahead);
        }
        $this->consume();
        return new AstNode($token);
    }

    private function matchOperatorAndOr()
    {
        $token = $this->lookahead;
        if (!$this->isOperatorAndOr($token->value)) {
            throw new ExpressionParsingException('Exception: Expected LIKE, OR, AND, =, <, >  but found ' . $this->lookahead);
        }

        $this->consume();
        return new AstNode($token);
    }

    private function matchOperatorLogic()
    {
        $token = $this->lookahead;
        if (!$this->isOperatorLogic($token->value)) {
            throw new Exception('Exception: Expected LIKE, OR, AND, =, <, >  but found ' . $this->lookahead);
        }
        // do we have a composite operator?
        $next = $this->peek();
        if (($next->type == Token::OPERATOR) && ($this->isOperatorLogic($token->value . $next->value))) {
            $token = new Token(Token::OPERATOR, $token->value . $next->value);
            $this->consume();
        }
        $this->consume();
        return new AstNode($token);
    }

    private function matchOperatorPlusMinus()
    {
        $token = $this->lookahead;
        if (!$this->isOperatorPlusMinus($token->value)) {
            throw new Exception('Exception: Expected * or / but found ' . $this->lookahead);
        }
        $this->consume();

        return new AstNode($token);
    }

    private function matchUnaryOp()
    {
        $token = $this->lookahead;
        if ($token->value == '-') {
            $this->consume();
            $unary = new AstNode(new Token(Token::UNARY_MINUS, '-'));
            $unary->addChild($this->matchTermLogic());
            return $unary;
        } else if ($token->value == '!') {
            $this->consume();
            $unary = new AstNode(new Token(Token::UNARY_NOT, '!'));
            $unary->addChild($this->matchTermLogic());
            return $unary;
        }

        throw new Exception ('Expected: -|! but found ' . $this->lookahead);
    }

    private function matchFunction()
    {
        $token = $this->lookahead;
        if ($token->type == Token::IDENTIFIER
            && $this->peek(0)->type == Token::LBRACK
        ) {
            $this->consume();
            $function = new AstNode(new Token(Token::FUNC, $token->value));
            $args = $this->matchArguments();
            foreach ($args as $arg) {
                $function->addChild($arg);
            }

            return $function;
        }
        throw new Exception ('Expected: Function but found: ' . $this->lookahead . '.');
    }

    // return the arguments directly as array,
    private function matchArguments()
    {
        if ($this->lookahead->type == Token::LBRACK) {
            $this->consume();
            $args = array();
            while ($this->lookahead->type !== Token::RBRACK && !$this->isEnd()) {
                $args[] = $this->matchExpression();
                if ($this->lookahead->type != TOKEN::RBRACK) {
                    $this->match(Token::SEMICOLON);
                }
            }
            $this->match(Token::RBRACK);
            return $args;
        }

        throw new ExpressionParsingException ('Expected: Argument list but found: ' . $this->lookahead);
    }

    // care about open/closing parenthesis
    private function matchParenthesizedExpression()
    {
        $this->match(Token::LBRACK);
        $current = $this->matchExpression();
        $this->match(Token::RBRACK);
        return $current;
    }

    private function next()
    {
        if (!count($this->buffer)) {
            return $this->input->next();
        }
        // we have buffered input from peek calls() left
        return array_shift($this->buffer);
    }

    // allows to inspect next element from input.
    // Peeked tokens are buffered up and given back by next, when queried next().
    private function peek($nr = null)
    {
        if ($nr !== null) {
            if (count($this->buffer) < $nr) {
                throw new Exception('No token in buffer at position: ' . $nr);
            }

            return $this->buffer[$nr];
        }
        $nextToken = $this->input->next();
        $this->buffer[] = $nextToken;

        return $nextToken;
    }

    private function isLookaheadOperatorAndOr()
    {
        return $this->isOperatorAndOr($this->lookahead->value);
    }

    private function isLookaheadOperatorMulDiv()
    {
        return $this->isOperatorMulDiv($this->lookahead->value);
    }

    private function isLookaheadOperatorPlusMinus()
    {
        return $this->isOperatorPlusMinus($this->lookahead->value);
    }

    private function isLookaheadOperatorLogic()
    {
        return $this->isOperatorLogic($this->lookahead->value);
    }

    private function isOperatorPlusMinus($possibleOp)
    {
        return in_array($possibleOp, array('+', '-'));
    }

    private function isOperatorMulDiv($possibleOp)
    {
        return in_array($possibleOp, array('*', '/'));
    }

    private function isOperatorAndOr($possibleOp)
    {
        return in_array($possibleOp, array('AND','OR', 'IN', 'LIKE'));
    }

    private function isOperatorLogic($possibleOp)
    {
        // check also for '!' to detect correctly in lookahead. '!='.
        return in_array($possibleOp, array('=', '>', '<', '<=', '>=', '!', '!='));
    }
}

/* Rules for the parser:
	EXPR 				--> TERM_LOGIC (OPERATOR_LOGIC TERM_LOGIC)*
    TERM_ANDOR          --> TEMR_LOGIC(OPERATOR_LOGIC TERM_LOGIC)
	TERM_LOGIC 			--> TERM_PLUSMINUS (OPERATOR_PLUSMINUS TERM_PLUSMINUS)*
	TERM_PLUSMINUS 		--> TERM_MULDIV (OPERATOR_MULDIV TERM_MULDIV)*
    TERM_MULDIV 		--> FUNCTION | ID | UNARY_OP
    OPERATOR_LOGIC 		--> 'AND' | 'OR' | 'LIKE' | '=' | '<' | '>', '!='
    OPERATOR_PLUSMINUS 	--> '+' | '-'
    OPERATOR_MULDIV 	--> '*' | '/'
    UNARY_OP 			--> '-' | '!' EXPR
    FUNCTION 			--> ID'(' ARGUMENTS ')'
    ARGUMENTS			--> (EXPRESSION(',' EXPRESSION)*)?
    ID					--> ([a-Z]|_)+('.'(ID))?
 */
