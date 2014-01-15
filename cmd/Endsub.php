<?php 
class Endsub extends AbstractStatement {
	public function execute($basic) {
		// do nothing
		//TODO: clean up scope should moved here.
	}
	
	public function parse(Parser $lexer, $basic) {
		// nothing to parse but ENDSUB.
	}
}
