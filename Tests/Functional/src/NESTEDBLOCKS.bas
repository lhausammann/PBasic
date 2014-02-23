fail = 0
success = 0
FOR i = 1 TO 10

    IF i = 5 THEN
        BREAK
        fail = 1
    ENDIF
NEXT i

FOR a = 1 TO 10
    FOR b = 1 TO 10
        LET blahblah = 5
        LET blittbB = RND(5)
        IF b = 3 THEN
            BREAK
            END
        ENDIF
    NEXT b
NEXT a

success = (fail=0) AND (i=5) AND (b=3) AND (a=10)
