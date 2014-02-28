<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Luzius Hausammann
 * Date: 24.02.14
 * Time: 19:47
 * To change this template use File | Settings | File Templates.
 */

use PBasic\Interpreter\Basic;

class GotoTest extends \PHPUnit_Framework_TestCase
{
    public function testGoto()
    {

        $file = __DIR__ . '/src/GOTO.bas';
        $b = new Basic($file);
        $b->runProgram();

        $result = $b->getVar('success');
        // $global = $b->getVar('global');
        // $this->assertEquals(true, $result);
        // $this->assertEquals(true, $global);
        // $stack = $b->getReturn();
    }
}
