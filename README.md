PBasic
======

An expermimental PHP Parser for a BASIC inspired language.
Start console.php to run some BASIC'ish scripts in your browser. 
Look into cmd folder to study existing commands.
Look in src folder to see some BASICish examples.

Disclaimer: This is rather experimental and not very stable, but fun :)

Statements:
 - PRINT <expr>
 - INTPUT <var>
 - GOTO
 - FOR <assignment> TO <expr> STEP <expr>
       <statements>
   NEXT <var>

   WHILE <expr>
    <statements>
   WEND

   Note: Its possible to use BREAK and CONTINUE in loops.

 - IF <expr> THEN
    <statenemts>
  ELSE
    <statements>
  ENDIF

  SUB(<parameterList)
    <statements>
  ENDSUB

  RETURN <var>

  Note: Parameters are given call-by-ref.




