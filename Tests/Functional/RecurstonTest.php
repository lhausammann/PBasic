<?php
use PBasic\Interpreter\Basic;

class RecursionTest extends \PHPUnit_Framework_TestCase
{
    public function testRuns()
    {
        return;
        $file = __DIR__ . '/src/COUNT.bas';
        $b = new Basic($file);
        $b->runProgram();
        $scope = $b->getScope();
        $i = $scope->getVar('a');
        $result = $scope->getVar('result');

        $this->assertEquals("result", $result);
    }
}
