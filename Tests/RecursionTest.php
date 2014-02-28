<?php
use PBasic\Interpreter\Basic;

class RecursionTest extends \PHPUnit_Framework_TestCase
{
    public function testRuns()
    {

        $file = __DIR__ . '/src/FNTEST.bas';
        $b = new Basic($file);
        $b->runProgram();

        $result = $b->getVar('success');
        // $hasFinished = $b->getVar('finished');

        $this->assertEquals("SUCCESS", $result);
        // $this->assertEquals(true, $hasFinished == true);
    }
}
