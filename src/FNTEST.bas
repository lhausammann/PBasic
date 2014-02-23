a = fn_1()

a = rec(10)


call rec_call(10) = b

if (a = b) THEN
    PRINT "SUCCESS"
ELSE
    PRINT "FAIL"
ENDIF

PRINT "After calling rec(10) recursively."

END

SUB fn_1()
    PRINT "fn_1"
    if 1 = 1 then
        return 1
    endif
    PRINT "NOT PASSED"
ENDSUB


SUB rec(n)
    IF (n > 0) THEN
        dummy = rec(n - 1)
        PRINT n
        RETURN dummy
    ELSE
        PRINT "no"
        RETURN
    ENDIF
    SCOPEDUMP
ENDSUB


sub rec_call(n)
    IF (n > 0) then
        print n

        call rec_call(n -1) = dummy
        input a
        print n
        RETURN dummy
    ELSE
        PRINT "end"
        RETURN
    ENDIF
ENDSUB

PRINT "END"


