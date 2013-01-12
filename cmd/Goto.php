<?php 
/** 
 * Note: This goto implementation, as it stands currently, will allow you to jump out 
 * of blocks. But never into a block.
 * @author luz
 *
 */
class BGoto extends AbstractStatement  {
	private $label;
	
	public function parse(Parser $parser, $basic) {
		$lexer = $parser->getLexer();
		$this->label = $this->matchNumber($lexer)->value;
	}
	
	public function execute($basic) {
		
		if ($basic->hasLabel($this->label)) {
			$basic->breakAll(); // break all loops
			$basic->jump($this->label);
		} else {
			throw new Exception ("Could not resolve label: " . $this->label);
		}
	}
	
	public function next($basic) {
		// leave all blocks
		$this->parent->terminateAll($basic); // reset all blocks
		$root = $this->parent;
		while ($root->parent) {
			$root = $root->parent;
		}
		
		return $root->jump($this->label, $basic);
	}
}
