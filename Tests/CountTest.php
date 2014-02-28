<?php
use PBasic\Interpreter\Basic;

class CountTest extends \PHPUnit_Framework_TestCase
{
    public function testRuns()
    {
        $file = __DIR__ . '/src/COUNT.bas';
        $basic = new Basic($file);
        $basic->runProgram();
        // get scope

        $scope = $basic->getScope();

        $i = $scope->getVar('success');

        $this->assertTrue($i == true);
    }
}