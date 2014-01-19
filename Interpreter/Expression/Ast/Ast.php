<?php
namespace PBasic\Interpreter\Expression\Ast;

class Ast
{
    const LEAVE = 0;
    const NODE = 1;
    static $nodeTypes = array(
        LEAVE => 'LEAVE',
        NODE => 'NODE',
    );
}
