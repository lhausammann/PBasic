<?php
require_once('Expression/ExpressionParser.php');
require_once('Expression/ExpressionVisitor.php');
// Test
function debug($mixed) {
	echo '<pre>';
	var_dump($mixed);
	echo '</pre>';

	
}

// Some Test cases
function execute($tree, $scop = array()) {
	static $scope;
	if (count($scop)) {
		$scope = $scop;
	}
	if ($tree->getNodeType()==Ast::LEAVE) {
		if ($tree->getType() == Token::IDENTIFIER) {
			
			return $scope[$tree->getValue()];
		}
		
		return $tree->getValue();
	} else {
		$op = $tree->getValue(); // fetch operator.
		$result = in_array($op, array('*', '/')) ? 1 : 0;

		if ($op == '-' && $tree->getType()==Token::UNARY_MINUS) {
			return -1 * execute ($tree->children[0]);
		}
		
		$isFirstChild = true;
		foreach ($tree->children as $child) {
			if ($isFirstChild) {
				$result = execute($child);
				$isFirstChild = false;
				continue;
			}
			switch ($op) {
				case 'AND' : $result = $result && execute ($child); break;
				case 'OR' : $result = $result || execute($child); break;
				
				case '!=' : $result = $result != execute ($child); break;
				case '=' : $result = $result == execute($child); break;
				case '<=' : $result = $result <= execute($child); break;
				case '>=' : $result = $result >= execute($child); break;
				case '>' : $result = $result > execute($child); break;
				case '<' : $result = $result < execute ($child); break;
				
				case '+' : $result = $result + execute($child); break;
				case '-' : $result = $result - execute($child); break;
				case '*' : $result = $result * execute($child); break;
				case '/' : $result = $result / execute($child); break;
			}
			
		}
		return $result;
	}
}



$tests = array(
	// Plus minus
	array('expr'	=> '1+1',					'expected'	=> 2), // addition/subtraction
	array('expr'	=> '5-2',					'expected'	=> 3),
	array('expr'	=> '5--2',					'expected'	=> 7),
	array('expr'		=> '5---2',				'expected'	=> 3), 
	array('expr' 	=> '10-2-3',				'expected'	=> 5),
	array('expr'	=> '10--(2-3)',				'expected'	=> 9), 
	// MulDiv
	array('expr' 	=> '6/2',					'expected'	=> 3),
	array('expr' 	=> '1 + 1 * 2', 			'expected' => 3),
	array ('expr' 	=> '2+6*2/4',				'expected' => 5),
	array ('expr' 	=> '(2+5)*7/7',				'expected' => 7),	
	array ('expr'	=> '2.5 + 2.5',				'expected' => 5),
	array ('expr'	=> '1 + 2*3*4',				'expected' => 25),
	array ('expr'	=> '(1 + 2)*3*((-4))',		'expected' => -36),
	array ('expr'	=> '-2* -2',				'expected' => 4 ),
	// Fails
	array ('expr'	=> '(1 + 2)*3*((4))))))',	'expected' => 'Exception'),
	array ('expr' 	=> ' (7*3',					'expected' => 'Exception'),
	// logical operators:
	array ('expr' 	=> ' AND 4',				'expected' => 'Exception'),
	array ('expr' 	=> '1+1=3',					'expected' => false),
	array ('expr' 	=> ' (7*3=21) AND (2*2!=5)','expected' => true),
	array ('expr' 	=> ' (7*3=21) AND (3+3=6)',	'expected' => true),
	array ('expr' 	=> ' (7*3=20) OR (3+3=6)',	'expected' => true),			
);

foreach ($tests as $test) {
	$parser = new ExpressionParser(new Lexer($test['expr']));
	$visitor = new ExpressionVisitor($parser);
	try {
		$ast = $parser->start();
		$result = $ast->accept($visitor);
		if ($result == $test['expected']) {
			echo '<p><font color="green"><strong>Passed:</strong> ' . $test['expr'] . '=' . $result . '</font></p>'; 
		} else {
			echo '<p><font color=red><strong>Failed:</strong> ' . $test['expr'] . '=' . $result . '</font> Expected was: '.$test['expected'].'</p>';
			debug ($test['expected']);
			debug($result);
		}
	} catch (Exception $e) {
		if ($test['expected']=='Exception') {
			echo '<p><font color="green"><strong>Passed expected Exception:</strong> ' . $test['expr'] . '=' . 'Exception' . '</font></p>'; 
		} else {
			echo '<p><font color=red><strong>Failed:</strong> ' . $test['expr'] . '=' . "Exception" . '</font> Expected was: '.$test['expected'].'</p>';
			debug ($e);
		}
	}
}

// Test scope:
$parser = new ExpressionParser(new Lexer('abc-1'));

// $parser->setScope(array('abc' => 2));
$tree = $parser->start();
echo execute($tree, array('abc' => 15));
echo execute($tree, array('abc' => 2));

// Test Lexer with Operators:
$test = '7=7+1';
$lexer = new Lexer($test);
while($token = $lexer->next()) {
	echo $token;
}

$test = 'LET a = 10';
$lexer = new Lexer($test);
while($token = $lexer->next()) {
	echo $token;
}
/*
$parser = new ExpressionParser(new Lexer($test));
$tree = $parser->start();
echo $tree;
execute($tree);
*/
