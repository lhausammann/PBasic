<?php
namespace PBasic\Interpreter;

use PBasic\Interpreter\Expression\Token;
use Exception;

class Lexer
{
    private $input;
    private $current = 0;
    private $isEnd = false;
    private $next = null;

    public function __construct($input)
    {
        $this->input = $input;
        $this->lookahead = $input{0};
    }

    public function setInput($input)
    {
        $this->input = $input;
        $this->lookahead = $input{0};
        $this->current = 0;
        $this->next = null;
        $this->isEnd = false;
        return $this;
    }

    public function start()
    {
        $result = array();
        while ($token = $this->next()) {
            $result[] = $token;

        }

        return $result;
    }

    // Sets a token to be the next token
    // when calling next(). Note: This will only work once!
    public function setNext(Token $token)
    {
        $this->next = $token;
    }

    /**
     * Returns the next token from an Input String.
     * 
     *
     */
    public function next()
    {
        if ($token = $this->next) {
            $this->next = null;
            return $token;
        }

        $this->skipWhitespace();
        $l = $this->lookahead;

        if (is_numeric($this->lookahead)) {
            $value = $this->parseNumber();

            return new Token(Token::NUMBER, $value);
        } else if ($this->isAlpha($this->lookahead)) {
            $id = $this->parseIdentifier();
            if ($this->isOperator($id)) {
                return new Token(Token::OPERATOR, strtoupper($id));
            }
            // its an identifier, if its not an operator.
            return new Token(Token::IDENTIFIER, $id);
        } else if ($this->lookahead == ',') {
            $this->consume();
            return new Token(Token::SEMICOLON, ',');
        } else if ($this->lookahead == '(') {
            $this->consume();

            return new Token(Token::LBRACK, $l);
        } else if ($this->lookahead == '"') {

            return new Token(Token::STRING, $this->parseString());
        } else if ($this->lookahead == ')') {
            $this->consume();

            return new Token(Token::RBRACK, ')');
        } else if ($this->lookahead == '?') {
            $this->consume();

            return new Token(Token::QUESTION, '?');
        } else if ($this->lookahead == '.') {
            $this->consume();

            return new Token(Token::PERIOD, '.');
        } else if ($this->lookahead == ':') {

            $this->consume();

            return new Token(Token::DOUBLE_POINT, ':'); // colon
        } else if ($this->lookahead == "'") {

            $this->consume();

            return new Token(Token::HIGH_COMMATA, "'");
        } else if ($this->isOperator($l)) {
            $this->consume();

            return new Token(Token::OPERATOR, $l);
        }

        // return end token
        if (!$this->isEnd && $this->isEnd()) {
            $this->lookahead = null;

            return new Token(Token::END, '');
        }
        // end state reached
        if ($this->isEnd) {

            // stop loops after end of file
            return null;
        }

        throw new Exception('Could not tokenize input: ' . $this->input . 'Trying to parse: ' . $this->lookahead);
    }

    protected function parseNumber()
    {
        $ret = '';
        $float = false;
        while (is_numeric($this->lookahead) || $this->lookahead === '.') {
            if ($this->lookahead == '.' && !$float) {
                // e.g. 3.45
                $float = true;
            } else if ($this->lookahead === '.') {
                // e.g. 3.1.4
                throw new Exception ('not well formed number: ' . $ret . $this->lookahead);
            }
            $ret .= $this->lookahead;
            $this->consume();
        }
        return $ret;
    }

    protected function consume()
    {
        if (strlen($this->input) > $this->current + 1) {
            $this->lookahead = $this->input{++$this->current};

        } else {
            $this->lookahead = null;
        }
    }

    protected function skipWhitespace()
    {
        $filter = array(10, 13, 32, 9);
        while ($this->lookahead === ' ' || in_array(ord($this->lookahead), $filter)) {

            $this->consume();
        }
    }

    protected function isAlpha($token)
    {
        return (ord('A') <= ord($token)) && (ord('z') >= ord($token) || ($token == '_'));
    }

    protected function isAlphaNumeric($token)
    {
        return $this->isAlpha($token) ? true : $token >= '0' && $token <= '9';
    }

    protected function parseIdentifier()
    {
        if (!$this->isAlpha($this->lookahead)) {
            throw new Exception('Parse error: Expected alpha but found: ' . $this->lookahead);
        }
        $buffer = '';

        while ($this->isAlphaNumeric($this->lookahead)) {
            $buffer .= $this->lookahead;
            $this->consume();
        }

        // check for ending strings ($)
        $peek = $this->lookahead;

        if ($peek == '$') {
            $this->consume();
            $buffer .= $peek;
        }

        return $buffer;
    }

    protected function parseString()
    {
        $startEnd = '"';
        if ($this->lookahead != '"') {
            throw new Exception("Parse error: Expected ' but found: " . $this->lookahead);
        }
        $this->consume();
        $buffer = '';
        while ($this->lookahead != $startEnd && !$this->isEnd()) {
            if ($this->lookahead == '\\') {
                $this->consume();
            }
            $buffer .= $this->lookahead;
            $this->consume();
        }
        if ($this->isEnd) {
            throw new Exception('Unexpeced end of file during tokenizing string.');
        }
        $this->consume();
        return $buffer;
    }

    protected function isOperator($op)
    {
        $op = strtoupper($op);
        $ops = array('+', '-', '/', '*', '<', '=', '>', 'AND', 'OR', 'LIKE', '!', 'IN');
        return in_array($op, $ops);
    }

    public function isEnd()
    {

        if ($this->isEnd) {
            return true;
        }

        return $this->isEnd = strlen($this->input) <= $this->current + 1;
    }
}