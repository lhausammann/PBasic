PRINT "HALLO"

LABEL 20
INPUT a
IF (a < 0) THEN
	PRINT "a groesser null"
	GOTO 20
ENDIF

WHILE (a > 0)
	a = a - 1
	COLOR ABS(a)
	PRINT "a ist", a
WEND
END