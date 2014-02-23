<?
use PBasic\Interpreter\Basic;

class ExpressionTest extends \PHPUnit_Framework_TestCase
{
public function testRuns()
{

$file = __DIR__ . '/src/EXPRESSION.bas';
$b = new Basic($file);
$b->runProgram();
$scope = $b->getScope();
$result = 1;
for ($i = 1; $i < 4; $i++) {
    $result = $result * $scope->getVar('success' . $i);
    $result = (int) $result;
    $this->assertEquals(1, $result, 'failed on success' . $i);
}
$result = (int) $result; // cast
$this->assertEquals(1, $result);
}
}
