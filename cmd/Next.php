<?php 
// while <expr> <list of statements>* wend
class Next extends AbstractStatement {
	private $var = null;
	
	public function execute($basic) {
		// do nothing
	}
	
	public function parse(Parser $lexer, $basic) {
		$possibleVar = $lexer->next();
		if ($possibleVar->type === Token::IDENTIFIER) {
			$this->var = $possibleVar->value;
		} else {
			//$lexer->setNext($possibleVar); // put it back to the lexer
		}
		//$this->matchEnd($lexer);
		
	}
	
	// parent (FOR) will check back if the var is the same as the running var, if given.
	public function getVar() {
		return $this->var;
	}
}
