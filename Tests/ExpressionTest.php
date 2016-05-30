<?
use PBasic\Interpreter\Basic;

class ExpressionTest extends \PHPUnit_Framework_TestCase
{
    public function testExpressions()
    {

        $file = __DIR__ . '/src/EXPRESSION.bas';
        $b = new Basic($file);
        $b->runProgram();
        $scope = $b->getScope();
        $result = 1;
        for ($i = 1; $i < 4; $i++) {
            $result = $result * $scope->getVar('success' . $i);
            $result = (int)$result;
            $this->assertEquals(1, $result, 'failed on success' . $i);
        }
        $result = (int)$result; // cast
        $this->assertEquals(1, $result);
        $this->assertEquals(1, $b->getVar('positive'));
        $this->assertEquals(false, $b->getVar('myFalse'));
        $this->assertTrue(true, $b->getVar('myTrue'));

        /*
        isParentThesisApplied = (5+1) * 3 = 18

        isFiveLowerEight = 5 < 8
        isFiveLowerEqualsEight = 5 <= 8

        isFiveGreaterFour = 5 > 4
        isFiveGreaterEqualsFour = 5 >= 4

        isFiveGreaterEqualsFive = 5 >= 5
        isFiveLowerEqualsFive = 5 <= 5
        ...

        */

        $checks = array('isParentThesisApplied', 'isFiveLowerEight', 'isFiveLowerEqualsEight',
            'isFiveGreaterFour', 'isFiveGreaterEqualsFour', 'isFiveGreaterEqualsFive',
            'isFiveLowerEqualsFive', 'isFiveEqualsFive', 'isFloat', 'areParenthesisApplied',
            'isCorrectPrecedenceORBeforeEquals', 'positive');

        foreach ($checks as $check) {
            $this->assertTrue($b->getVar($check) == true, 'Failed on ' . $check);
        }
    }
}
