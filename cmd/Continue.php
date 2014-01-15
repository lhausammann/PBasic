<?php 
class BContinue extends AbstractStatement {
	public function execute($basic) {
		
	}
	
	
	
	public function parse(Parser $lexer, $basic) {
		// no arguments to parse here.
		return;
	}
	
	/**
	 * Find the next loop to break.
	 * @param AbstractStatement next statement to execute.
	 */
	public function next($basic) {
		// find the next look to break
		$p = $this->parent;
		while ($p && $p->isLoop() == false) {
			$p = $p->parent;
			
		}
		// TODO: Handle SUBs here also
		$p->current = count($this->parent->statements) -1; // jump to end of parent and
		return $p->next($basic);
		
		throw new Exception ("Can only break loops.");
		
	}
}
