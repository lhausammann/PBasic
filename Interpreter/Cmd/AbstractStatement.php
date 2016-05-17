<?php
namespace PBasic\Interpreter\Cmd;

use PBasic\Interpreter\Parser;
use PBasic\Interpreter\Expression\Token;
use Exception;

abstract class AbstractStatement
{
    protected $parent = null;
    protected $statementName;
    protected $lineNr;
    protected $instrNr;
    protected $blockNr;
    protected $isExecutable = true;
    protected $nr;
    protected $lineLabel = false;

    protected static $currentNr = 0;

    abstract public function parse(Parser $parser, $basic);

    abstract public function execute($basic);

    public function __construct($name, $instrNr, $lineNr, $blockNr)
    {
        //$this->instrNr = $instrNr;
        $this->lineNr = $lineNr;
        $this->statementName = $name;
        $this->blockNr = $blockNr;
        $this->nr = self::$currentNr++;
    }

    public function setLineLabel($label) {
//        echo "label:" . $label;
        return $this->lineLabel = $label;
    }

    public function getLineLabel() {
        return $this->lineLabel;
    }

    public function isExecutable()
    {
        return $this->isExecutable;
    }

    public function setParent($stat)
    {
        $this->parent = $stat;
    }

    public function getName()
    {
        return $this->statementName;
    }

    public function errorInfo()
    {
        return 'Error found in Statement: ' . $this->statementName . ' on line: ' . $this->lineNr;
    }

    public function update($statement)
    {
    }

    public function statementParsed($stat)
    {

    }

    public function __toString()
    {
        return $this->getName() . ' ' . $this->lineNr . '<br />';
    }

    public function match($chars, $lexer, $caseSensitive = false)
    {
        $val = $lexer->next()->value;
        if (!$caseSensitive) {
            $chars = strtoupper($chars);
            $val = strtoupper($val);
        }
        $match = $chars === $val;
        if (!$match) {

            throw new Exception("Expected: " . $chars . " but found: " . $val . $this->errorInfo());
        }

        return $match;
    }

    public function matchIdentifier($lexer)
    {
        $token = $lexer->next();
        if ($token->type != Token::IDENTIFIER) {
            throw new Exception("Expected: IDENTIFIER" . " but found: " . $token . $this->errorInfo());
        }

        return $token;
    }

    public function tryMatchIdentifier($lexer)
    {
        $token = $lexer->next();
        $lexer->setNext($token);
        if ($token->type == Token::IDENTIFIER) {
            return true;
        } else {
            return false;
        }
    }

    public function tryMatchNumber($lexer)
    {
        $token = $lexer->next();
        $lexer->setNext($token); // put it back.
        if ($token->type == Token::NUMBER) {
            return true;
        } else {
            return false;
        }
    }



    public function assertClass($className, AbstractStatement $stat)
    {
        if ($className === $stat->getName()) {
            return;
        }
        throw new Exception("Found statement " . $stat->getName() . ' but expected ' . $className . $this->errorInfo());
    }

    public function matchEnd($lexer)
    {

        $token = $lexer->next();

        if (!$token) { // already reached end
            return;
        }

        if ($token->value === "'") { // starting comment = END
            return;
        }

        if ($token->type === Token::END) {
            return;
        }

        throw new Exception("Expected: Token::END but found: " . $token . $this->errorInfo());
    }

    public function isEol($token)
    {

        return $token->type === Token::DOUBLE_POINT;
    }

    public function matchEol($lexer)
    {
        $token = $lexer->next();

        if ($token->type === Token::DOUBLE_POINT) {
            return;
        }
        throw new Exception("Expected was end of line, but fount was: " . $token);
    }

    public function matchNumber($lexer)
    {
        $token = $lexer->next();
        if ($token->type != Token::NUMBER) {
            throw new Exception("Expected: Token::Number" . " but found: " . $token . $this->errorInfo());
        }

        return $token;
    }

    public function setAsCurrent($basic, $stat = null)
    {
        if (!$stat) {
            $stat = $this;
        }
        return $this->parent->setAsCurrent($basic, $stat);
    }

    public function next($basic)
    {
        if ($this->parent) {
            //$next = $this->parent->next($basic);
            $next = $this->parent->next($basic);
            return $next;
        }

        return null;
    }

    public function findByInstrNr($nr)
    {
        if ($this->nr == $nr) {
            return $this;
        }
        return false;
    }
}
