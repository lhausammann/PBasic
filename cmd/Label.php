<?php 
class Label extends AbstractStatement {
	protected $label = null;
	
	public function parse(Parser $parser, $basic) {
		$lexer = $parser->getLexer();
		$name = $this->matchNumber($lexer)->value;
		$this->label = $name;
		$basic->addLabel($name, $this->blockNr);
	}
	
	public function execute($basic) {
		$basic->reachedLabel();
		
	}
	
	public function matchNumber($lexer) {
		$token = $lexer->next();
		if ($token->type != Token::NUMBER) {
			throw new Exception("Expected: IDENTIFIER" . " but found: " . $token);
		}
		
		return $token;
	}
	
	public function getLabel() {
		return $this->label;
	}
	
	
}
