outer = 0
FOR i = 1 TO 10
    IF MOD(i,  2) THEN
        CONTINUE
    ENDIF
    outer = outer + 1
    
    FOR j = 1 TO 10
        IF j = 5 THEN 
            BREAK 
        ENDIF
    NEXT j
NEXT i

result = (outer = 5) AND (i = 5)
