<?php

namespace PBasic;

use PBasic\Interpreter\Exception\RuntimeException;

use PBasic\Interpreter\Basic;

class CountTest extends \PHPUnit_Framework_TestCase
{
    public function testRunException()
    {
        $file = __DIR__ . '/src/EXCEPTIONS.bas';
        try {
            $basic = new Basic($file);
            $basic->runProgram();
        } catch (RuntimeException $e) {
            echo $e->getMessage();
            // pass
            $this->assertInstanceOf('PBasic\\Interpreter\\Exception\\RuntimeException', $e);
        }
    }
}