PBasic
======

<img src="https://travis-ci.org/lhausammann/PBasic.svg?branch=master" />

An experimental PHP Parser for a BASIC inspired language running in a browser.


    10 PRINT "Wie ist Dein Name?"
    INPUT a$
    IF a$="end" THEN 30
    PRINT "Hallo", a$ 
    GOTO 10
    30 END

Statements:
 - LET var=value
 - PRINT expr
 - INPUT var
 - RETURN var
 - GOTO label / GOSUB label
 - FOR <assignment> TO <expr> STEP <expr>
       statements
 -  NEXT <var>
 -  WHILE <expr>
 -   <statements>
 -  WEND
 -  iF THEN ELSE ENDIF

 - SUB(params)
     statements
 - ENDSUB

