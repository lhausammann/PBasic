a = rec(10)


call rec_call(10) = b

success = false

if (a = b) THEN
    success = "SUCCESS"
ELSE
    success = "FAIL"
ENDIF

PRINT "After calling rec(10) recursively."
PRINT "Success was: ", success

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
        PRINT n
        dummy = rec(n - 1)
        PRINT n
        RETURN dummy
    ELSE
        RETURN
    ENDIF
ENDSUB


sub rec_call(n)
    IF (n > 0) then
        PRINT n
        PRINT "interrput"
        INPUT b
        call rec_call(n -1) = dummy
        RETURN dummy
    ELSE
        PRINT n
        RETURN
    ENDIF
ENDSUB



