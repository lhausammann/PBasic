a = 10
WHILE (a < 20)
	PRINT a * 5
	LET a = a + 1
WEND

10 INPUT "Dein Name?", a
IF is_numeric(a) THEN
	GOTO 10
ENDIF


PRINT "Hallo", a
COLOR 5,5
PRINT "1"
ret = test()
PRINT ret

FOR x = 1 TO 10
	COLOR x
	PRINT x
NEXT x

PRINT 2
PRINT 7 * 5
SCOPEDUMP
END

SUB test()
    PRINT "calling test2"
	CALL test2
	COLOR rnd(0,15)
	PRINT "in test test"
	RETURN "blah"
ENDSUB

sub test2
	PRINT "enter test 2"
	RETURN "some thing"
ENDSUB