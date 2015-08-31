PBasic
======

An expermimental PHP Parser for a BASIC inspired language.
Start console.php to run some BASIC'ish scripts in your browser. 

10 PRINT "Wie ist Dein Name?"
INPUT a$
IF a$="end" THEN 30
PRINT "Hallo", a$ 
GOTO 10
30 END

Statements:
 - PRINT <expr>
 - INPUT <var>
 - GOTO
 - FOR <assignment> TO <expr> STEP <expr>
       <statements>
   NEXT <var>

   WHILE <expr>
    <statements>
   WEND

   SUB(params)
     <statements>
   ENDSUB

