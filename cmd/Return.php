<?php 
class BReturn extends AbstractStatement {
	private $exprTree = null;
	private $fn = null;
	
	public function parse(Parser $parser, $basic) {
		if ($token = $parser->next()) {
			if ($token->type == Token::END) {
				$this->exprTree = null;
			} else {
				$parser->getLexer()->setNext($token);
				//$parser->setNext($token); // put it back
				$this->exprTree = $parser->matchExpression();
			}
		}
		// $this->exprTree = $parser->matchExpression();
		$this->matchEnd($parser);
	}
	
	public function execute($basic) {
		
		$this->fn->forceEnd($basic); // jump to endsub statement
		$return = $basic->evaluateExpression($this->exprTree);
		$this->fn->setReturnValue($return, $basic);
	}
	
	public function setFunction($fn) {
		$this->fn = $fn;
	}
	
	public function next($basic) {
		// return the next statement from the function,
		// which will be ENDSUB.
		// Scope is cleaned up, and all loops are terminated by this.
		
		$next = $this->fn->next($basic); 
		return $next;
	}
}
