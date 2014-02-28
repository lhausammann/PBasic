<?php

use PBasic\Interpreter\Basic;

class BreakContinueTest extends \PHPUnit_Framework_TestCase
{

    public function testBreakContine()
    {
        $file = __DIR__ . '/src/BREAKCONTINUE.bas';
        $b = new Basic($file);
        $b->runProgram();

        $result = $b->getVar('result');

        $this->assertTrue(true, $result == true);
    }
}
