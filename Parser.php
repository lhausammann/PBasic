<?php


require_once('cmd/AbstractStatement.php');
require_once('cmd/AbstractBlockStatement.php');
require_once('cmd/Block.php');

require_once('Program.php');
require_once('cmd/Input.php');
require_once('cmd/Let.php'); // LET <IDENTIFIER> = <EXPR>
require_once('cmd/Scopedump.php'); // SCOPEDUMP
require_once('cmd/Print.php'); // print <EXPR>
require_once('cmd/If.php'); // IF (<EXPR>) THEN <statements> [ELSE <statements>] ENDIF
require_once('cmd/Endif.php'); // IF (<EXPR>) THEN <statements> [ELSE <statements>] ENDIF
require_once('cmd/Else.php'); // IF (<EXPR>) THEN <statements> [ELSE <statements>] ENDIF

require_once('cmd/While.php'); // WHILE <EXPR> <statements> WEND
require_once('cmd/Wend.php');
require_once('cmd/Color.php'); // COLOR <EXPR> [,<EXPR>]
require_once('cmd/Break.php');
require_once('cmd/Continue.php');
require_once('cmd/Label.php'); // LABEL <INT>
require_once('cmd/Goto.php'); // GOTO <INT>
require_once('cmd/For.php'); // FOR <IDENTIFIER>=<EXPRESSION> TO <EXPRESSION> STEP <EXPRESSION>
require_once('cmd/Next.php'); // NEXT [<IDENTIFIER>]
require_once('cmd/Call.php'); // CALL IDENTIFIER(<ARGLIST>)
require_once('cmd/Sub.php'); // SUB IDENTIFIER(<IDENTIFIER>[(,<IDENTIFIER>)*]
require_once('cmd/Endsub.php');
require_once('cmd/Return.php'); // RETURN <EXPRESSION>
require_once('cmd/End.php'); // RETURN <EXPRESSION>


interface Parser {
	public function parse($input = null);
}

class BasicParser implements Parser {
	private $comments = array(
		'REM',
		"'",
		"//",
		"",
	);

	private $input;
	private $lexer;
	private $current = 0;

	private $currentInstr = 0;
	private $instNr = 0;

	private $basic;
	private $observers = array();

	public function __construct($lexer, $basic) {
		$this->lexer = new Lexer(" ");
		$this->basic = $basic;
	}

	public function setInput($input) {
		$this->input = $input;
	}
	
	public function getLexer() {
		return $this->lexer;
	}

	public function parse($input = null) {
		$input = implode($input, ':') . ':';
		
		if ($input) {
			$this->input = $input;
		}
		$this->lexer = new Lexer($input);
		// set up the program 
		$program = new Program('root', 1,1,1);
		return $program->parse($this, $this->basic);
	}

	public function interpret($string, $basic) {
			
		$stat = $this->parseLine($string);
		$stat->execute($basic);
	}

	public function next() {
		
		return $this->lexer->next();
	}

	private function nextLine() {
		
			$next = $this->input[$this->current];
			$this->current++;

			return $next;		
	}

	// registers an observer during the parse process.
	public function addObserver(AbstractStatement $statement) {
		$this->observers[] = $statement;
	}

	public function removeObserver(AbstractStatement $statement) {
		$observers = array();
		foreach ($this->observers as &$observer) {
			if ($observer === $statement) {
				continue;
			}
			$observers[] = $observer;
		}

		$this->observers = $observers;
	}

	public function notify($statement) {
		foreach ($this->observers as $observer) {
			$observer->update($statement);
		}
	}

	public function matchExpression() {
		$exprParser = new ExpressionParser($this->lexer);
		try {	
			$tree = $exprParser->matchExpression();
			// configure the lexer to return the parsers current lookahead as next token:
			$this->lexer->setNext($exprParser->getLookahead());
		} catch (Exception $e) {
			
			throw new Exception("Parse error in expression on line: " . $this->current . ': ' . $e->getMessage());
		}
		return $tree;
	}

	// parses a file or a block until the statement stopStatement (or to the end, if none given).
	// if a parent statement is given, it will be notified by parseStatement about
	// each statement parsed in this block. (not about parsed statements in a block, e.g
	// an else statement in an if block. Parent only gets notified about if.)
	// The parent also get notified if its registered as an observer about
	// _every_ statement parsed until it reaches the stop statement.
	public function parseUntil($stopStatement = false, $parent = null) {
		$stats = array();
		while($stat = $this->nextStatement()) {
			
			if ($parent)
				$parent->addChild($stat);
			$this->notify($stat); // notify observers.
			if ($parent) {
				$parent->statementParsed($stat);
				
			}
			
			$stats[] = $stat;
			
			if (strtoupper($stat->getName()) === strtoupper($stopStatement)) {
				break;
			}

			if (! $stopStatement) {
				$this->currentInstr++;
			}
		}

		if ($parent) {
			// notify the parent of end of parsing.
			// Parsed stopStatement is given back to complete block parsing.
			$parent->endBlock($stat);
		}

		return $stats;
	}

	public function nextStatement() {
		$next = $this->lexer->next();
		if (! $next) {
			return null;
		}
		// skip ::::
		while($next->type == Token::DOUBLE_POINT) {
			$next = $this->next();
		}
		
		if ($next->value=="'") {
			// skip comment
			while($next->value != ':') {
				$next = $this->next();
			}
			
			return $this->nextStatement();
		}
		
		if ($next->type == Token::END) {
			
			return null;
		}
		
		$stat = null;
		if ($next) {
			$stat = $this->createStatement($next);
		}
		
		return $stat;
	}

	private function parseLine($line) {
		$stat = $this->lexer->next();

		//$stat = $cmdToken->value;
		if ($line) {
			$statement = $this->createStatement($stat);
			return $statement;
		}
	}

	private function isComment($line) {
		foreach ($this->comments as $commentSign) {
			// TODO: Fix this
			if (stripos ($line, $commentSign ) !== false) {
				return true;
			}
		}
		return false;
	}

	private function createStatement($statement) {
		$statementName = $statement->value;
		if ($statementName == ':' ) {
			return null;
		}
		// $className = "";
		// $statements = $this->lexer->setInput($line);
		//$statement = $this->lexer->next();
		
		//$statementName = $statement->value;
		
		$upper = strtoupper($statementName{0});
		$lower = strtolower(substr($statementName,1));
		$className = $this->getCmdClass($upper . $lower);
		if (! $className) {
			
			// let is optional keyword
			$className = 'Let';
			$this->lexer->setNext($statement); // put statement back
		}
		$lineNr = $this->current;
		$this->instrNr++;
		$stat = new $className(strtoupper($statementName), $this->instrNr, $lineNr, $this->currentInstr);
			
		$stat->parse($this, $this->basic);
		return $stat;
	}

	private function getCmdClass($className) {
		$reserved = array('Print', 'If', 'While', 'Break', 'Goto', 'For', 'Return', 'Endif', 'Else');
		if (in_array($className, $reserved)) {
			return 'B' . $className; // Prefix classname to not conflict with php keywords.
		}
		
		// Basic shortcut for print
		if ($className == '?') {
			return 'BPrint';
		}
		
		// No class found
		if (! class_exists($className)) {
			$className = '';
		}
		
		return $className;
	}
}