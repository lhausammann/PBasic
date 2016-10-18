<?php
namespace Example;


require('vendor/autoload.php');
use PBasic\Interpreter\Basic;
use PBasic\Interpreter\Cmd\AbstractStatement;
use AbstractBlockStatement;
// always initialize a session if you use input
session_start();

// the program to run
$file = <<<EOT
05 COLOR 3,8
10 PRINT "Wie ist Dein Vor- und Nachname?"
20 INPUT "Wie ist Dein Name?", name$
30 PRINT "Hallo", name$
40 PRINT "Noch einmal(y|n)?"
50 INPUT n$
60 IF n$="y" THEN
70  GOTO 10
80 ENDIF
80 PRINT "Bye!" 
EOT;


Basic::run($file);
