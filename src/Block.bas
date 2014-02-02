REM outer block
FOR a = 1 TO 3
    PRINT "A ist am Anfang: ", a
    REM inner block executions
    FOR i = 1 TO 15
        COLOR i, RAND(1,15)
        PRINT i
        ' GOTO 10 ' does not work yet
        IF i = 10 THEN
            PRINT "BREAKING BLOCK ON i=", i
            BREAK
            END ' should not happen
        ENDIF
    NEXT i
    PRINT "Continue: Press any key", a
    10 INPUT z
    PRINT "A ist am Ende: ", a
NEXT a
PRINT "A IST:", a