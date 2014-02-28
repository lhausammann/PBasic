fail = 0
success = 0

FOR i = 1 TO 10
    IF i = 5 THEN
        PRINT "i = 5, breaking loop: ", i
        BREAK
        fail = 1
    ENDIF
NEXT i

IF (fail) THEN
    PRINT "Failed breaking block"
ENDIF


