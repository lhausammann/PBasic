PRINT "START"
? "Starting with ?" 
LET x = SIMPLE(3)
PRINT SIMPLE(4)
PRINT SIMPLE(5)
SCOPEDUMP
PRINT x

PRINT FAC(7)

CALL FACCALL(7) = fac7
PRINT fac7


CALL COUNT(10)
 
i = 0
PRINT i
' skip tests







SCOPEDUMP
INPUT "Fakultät von?", n
CALL FAC(n) = fac
PRINT "Fakultät von ",n, " ist" , fac

' END is required because SUBs will get executed immediatly otherwise.

END

' No input allowed
SUB FAC(n)
	IF n <= 1 THEN
		RETURN 1
	ENDIF
	fac = FAC(n-1)
	RETURN fac * n
ENDSUB 

' Input allowed by using CALL() =
SUB FACCALL(n)
	PRINT "intterrupt", n
	INPUT x
	LET fac = 0
	IF n<=1 THEN
		RETURN 1
	ENDIF
	CALL FACCALL (n-1) = fac
	RETURN fac * n
ENDSUB
	


SUB COUNT(n)
	PRINT n
	IF n = 0 THEN 
		RETURN 0
	ENDIF
	CALL COUNT(n-1)
ENDSUB

SUB SIMPLE(n)
	return SIMPLE2(n)
ENDSUB

SUB SIMPLE2(n)
	return n*n
ENDSUB