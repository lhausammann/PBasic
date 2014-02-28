a = rec(3)


call rec_call(3) = b

success = false

if (a = b) THEN
    success = "SUCCESS"
ELSE
    success = "FAIL"
ENDIF



SUB fn_1()
    if 1 = 1 then
        return 1
    endif
    PRINT "NOT PASSED"
ENDSUB


SUB rec(n)
    IF (n > 0) THEN
        dummy = rec(n - 1)
        RETURN dummy
    ELSE
        RETURN
    ENDIF
ENDSUB


sub rec_call(n)
    IF (n > 0) then
        call rec_call(n -1) = dummy
        RETURN dummy
    ELSE
        RETURN
    ENDIF
ENDSUB

finished = 1



