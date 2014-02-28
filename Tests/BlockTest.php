<?php

use PBasic\Interpreter\Basic;

class BlockTestr extends \PHPUnit_Framework_TestCase
{
    public function testRuns()
    {

        $file = __DIR__ . '/src/NESTEDBLOCKS.bas';
        $basic = new Basic($file);
        $basic->runProgram();
        // get scope

        $scope = $basic->getScope();

        $i = $scope->getVar('i');
        $fail = $scope->getVar('fail');
        $success = $scope->getVar('success');
        $this->assertEquals($i, 5);
        $this->assertTrue($fail == 0);
        $this->assertTrue($success, 1);
    }
}
