a = 0
b = 0


FOR i = 0 TO 100 STEP 20
    IF i < 10 THEN
        a = a + 1
    ELSE
        b = b + 1
    ENDIF
NEXT i

result = (a = 1) AND (b = 4)
