<?php
use PBasic\Interpreter\Basic;

class IfElseTest extends \PHPUnit_Framework_TestCase
{

    public function testRuns()
    {
        $file = __DIR__ . '/src/IFELSE.bas';
        $b = new Basic($file);
        $b->runProgram();


        $result = $b->getVar('result');

        $this->assertTrue(true, $result == true);
    }
}
