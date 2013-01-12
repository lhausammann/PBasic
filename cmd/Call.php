<?php
class Call extends AbstractStatement  {
	private $name;
	private $paramTrees = array();
	private $out = '';


	public function parse(Parser $lexer, $basic) {
		$this->name = $this->matchIdentifier($lexer)->value;
		$this->createParamList($lexer, $basic);
		if ($token = $lexer->next()) {
			
			if ($token->value == '=') {
				$this->out = $this->matchIdentifier($lexer)->value;
			}
		}
	}

	public function execute($basic) {
	
		return;
		$basic->setBreak(false);
		$args = array();
		foreach ($this->paramTrees as $paramTree) {
			$args[] = $basic->evaluateExpression($paramTree);
		}


		$fn = $basic->getSub($this->name);
		$fn->executeSub($args, $basic);
		$basic->setBreakAll(false);
	}

	private function createParamList($parser, $basic) {
		$token = $parser->next();
		if ($token->type === Token::END) {
			return;
		} else if ($token->value === "(") {
			$this->paramTrees[] = $parser->matchExpression();
			$token = $parser->next();

			while((',' === $token->value)) {
				$this->paramTrees[] = $parser->matchExpression($token);
				$token = $parser->next();
			}

			if ($token->value!==')') {
				throw new Exception('Error parsing Call statement: Expected ) but found: ' .  $token->value . $this->errorInfo());
			}
			
			return;
		} else {
			$parser->getLexer()->setNext($token); // put it back.
		}
	}
	
	public function next($basic) {
		/*
		
		if ($fn->isExecuting($basic)) {
			$next = $fn->next($basic);
			return $next;
		} */
		
		// add the parent of call statement as sub parent to 
		// return to this point after executing.
		
		$fn = $basic->getSub($this->name);
		
		if ($this->out) {
			$fn->setReturnVar($this->out);
		}
		
		// $fn->addReturn($this->parent); // store return object on stack to continue after call has ended.
		$args = array();
		foreach ($this->paramTrees as $paramTree) {
			$args[] = $basic->evaluateExpression($paramTree);
		}

		$fn->setArguments($args, $basic);
		$fn->start($this, $basic);
		$next = $fn->next($basic);
		return $next;
	}
		
}