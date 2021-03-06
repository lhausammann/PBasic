PBasic
======

<img src="https://travis-ci.org/lhausammann/PBasic.svg?branch=master" />

An experimental PHP Parser for a BASIC inspired language running in a browser.

## Usage
To run a program pass a String to Basic::run():
*file helloBasic.php*
```php
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
```

Or pass a path to Basic::run
`Basic::run($filePath);` 

You can find some examples in the src directory and see how to run them in the index.php example file.

## Statements:

 - DIM var(length)
 - DATA value,value
 - READ value
 - RESTORE
 - LET var=value (optional keyword)
 - PRINT expr
 - INPUT msg, var
 - COLOR foreground, background
 - RETURN var
 - GOTO label / GOSUB label
 - FOR assignment TO expr STEP expr
       - statements
 - NEXT expr
 - WHILE expr
     - statements
 - WEND
 - iF THEN ELSE ENDIF
 - SUB(params)
     - statements
 - ENDSUB

Recursion can be used, but its recommended to not use it in Expressions when using INPUT (see below).

## Features
 - Recursion on SUBs (using INPUT as well)
 - Glue language: PHP functions can be used with CALL / in expressions (this is a security issue in real live solutions)
 - SCOPEDUMP allows dump of all scopes
 - FOR works with -steps when not specified: FOR 10 TO 1
 - Implementation of dynamic GOTOs (a=10:GOTO a) should be possible (when a contains a number), which would be a new concept. Not implemented in the parser yet. (Yes, its an evil concept :)
 - Retrieval of the scope is possible after execution of the script took place => see functional tests for an example of that.
 
## Differences from other basic implementations:

- To ease the lexing and parsing, all tokens are one-worded. PBasic has ENDSUB ENDIF in one word, not END SUB / END IF.
- Its a tree-based interpreter. To ease the interpreting, goto only allows to jump out (quit) blocks, but not to jump into a block.
- INPUT is handled by storing the actual scope in to a session. So PBasic requires a session started when using INPUT.
- SUB / ENDSUB behaves like a php function using return rather than a basic SUB / FUNCTION returning none / the value of its name. Sub uses call-by-value (while qBasic used call-by-value, as far as I know).
- DIM is only used for Arrays (one dimension).
- RESTORE does not take a label.
- Because PBasic executes expressions on-the-fly (other than statements block which can be stopped using INPUT) its not recommended to use functions in an expression which contains INPUT statements, because that leaves to unexpected behaviour (skipping the input statement and running the evaluation without INPUT). It is, however, possible to assign a return value when using CALL using INPUT in myFunc:
CALL myFunc = myValue. 

## Plugin system
Each command consists of a parsing/executing block. You can add simple statements easily (see e.g. BPrint.php for an example).
Block statements are a bit more difficult to add, and in the parsing process you have to parse the children from the block as well (you can use parseUntil('nameOfEndBlockStatement') to parse the whole block and observe the parsed blocks during this time. See BFor.php for a complex example in Cmd folder.
Executing blocks is handled by returning the next statement. This means, you must handle state issues in the current Scope so it can be serialized to the session if needed.

## Scopes & Functions & Variables
PBasic has a global and a NestedScope. NestedScopes are used by Function calls providing a new Scope for each call. If you don't need local (nested) scopes, use GOSUB instead (acts as a SUB but does not add a new Scope onto the stack).
Variables are put on the current stack. There are some global system variables (which can be seen when using SCOPEDUMP) but which are not accessible (they start with a number).


