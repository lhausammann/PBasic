<?php 
class BPrint extends AbstractStatement {
	private $expr = array();
	private $spaceOrTab = array();
	public function execute($basic) {
		$msg = '';
		$s = $this->spaceOrTab;
		foreach ($this->expr as $e) {
			$msg .= $basic->evaluateExpression($e);
			$sep = array_shift($s);
			if ($sep == ',') {
				$msg .= "\t";
			} else {
				$msg .= " ";
			}
		}
		
		echo '<div class="print" style="color:' . $basic->getForegroundColor() . ';background-color:' .$basic->getBackgroundColor(). ';display:block;width:100%">';
		echo '' .$msg . '</div>';	
	}
	
	public function parse(Parser $parser, $basic) {
		$this->expr[] = $parser->matchExpression();
		// PRINT a,b,c, ...
		$lexer = $parser->getLexer();
		while($token = $lexer->next()) {
			if ($token->value==',' || $token->value==';') {
				$this->expr[] = $parser->matchExpression();
				$this->spaceOrTab[] = $token->value;
			}
		}
		
	}
}
