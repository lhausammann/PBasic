a = fn_1()

rec = rec(10)
call rec_call(10) = rec_call

if (rec = rec_call AND a="success") THEN
    result = "SUCCESS"
ELSE
    result = "FAIL"
ENDIF



SUB fn_1()
    if 1 = 1 then
        return 1
    endif
ENDSUB


SUB rec(n)
    IF (n > 0) THEN
        dummy = rec(n - 1)
        PRINT n
        RETURN dummy
    ELSE
        RETURN
        END
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