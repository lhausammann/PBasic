<?php 

class Scopedump extends AbstractStatement  {
	
	public function parse(Parser $parser, $basic) {
		// nothing to do
		$this->matchEnd($parser);
	}
	
	public function execute($basic) {
		echo "Break all set: " . $basic->breakAll();
		echo "Break set: " . $basic->isBreak();
		$basic->dumpScope();
	}
}