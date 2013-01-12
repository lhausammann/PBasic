REM my first program
REM -------------------------------------------
REM allowed statements are:
REM PRINT (only one expression), COLOR, LET (required), WHILE-WEND,
REM IF THEN ELSE ENDIF (THEN / ENDIF required)
REM LABEL/GOTO (only in non-block structures allowed.)
REM FOR/NEXT
REM SCOPEDUMP (prints out global scope)
REM Implenented functions are:
REM RND(min, max)
REM sqrt(<epr>)
REM and all native php functions.
REM -------------------------------------------


PRINT 5+7

LET name = 10
PRINT "Dein Name ist: "
PRINT name
SCOPEDUMP
PRINT fac(5)
SCOPEDUMP
LET c = 30
PRINT c
CALL multiplyTwo(2)

PRINT "HaLLO"




FOR i=1 TO 3 
	
	COLOR 2
	FOR j=1 to 5
		FOR k=1 TO 10
			COLOR 5
			PRINT i * j * k
			COLOR 2
			BREAK
		NEXT k
	NEXT j	
NEXT i



CALL fn1(2,3)
SCOPEDUMP




LABEL 33
PRINT LCFIRST ("Hallo")

CALL multiply(4,5)
LET n = fac(5)
PRINT n

FOR x = 1 TO 30 STEP 1
	FOR j=2 TO -5 STEP -1
		PRINT "HIER" + j * x 
		PRINT x = 3
		IF x = 4 THEN
			PRINT "exiting loop at x=" + x
			GOTO 2
			PRINT "Should never be printed."
		ENDIF
	NEXT j
NEXT

PRINT "TEST NESTED LOOP ENDED REGULARLY"

LABEL 2
PRINT "Label 2"

PRINT "HIER"
IF 1 THEN
	PRINT "blubb"
ENDIF


LET a = 2
LET comment = ""

WHILE a < 20
	
	LET a = a + 1
	IF MOD(a,2)=1 THEN
		COLOR 2,12
		LET comment = " ungerade Zahl"
	ELSE
		COLOR 4 , 8
		LET comment = " gerade Zahl"
	ENDIF
	PRINT a, " ist eine ", comment
WEND

LET c = 0
WHILE c <= 15
	
	COLOR c,12
	PRINT "Farbcode: " + c
	LET c = c + 1
WEND
END
PRINT "RANDOM"
PRINT RND()

REM ----------------------------------------
REM Spaghetti
LET d = 0
LABEL 10
	PRINT "d ist: " + d
	LET d = d + 10
	IF d = 30 THEN
		PRINT "HIER"
		PRINT "BIN"
		PRINT "ICH NOCH"
		GOTO 10000
	ENDIF
GOTO 10


LABEL 10000
END

SUB multiplyTwo(x)
	PRINT x * 2
	RETURN x * 2
	LET c = 5
ENDSUB


SUB test(a)
	return a
ENDSUB

SUB fac(n)
	IF n > 1 THEN
		RETURN n * fac(n-1) 
	ENDIF
	RETURN 1
ENDSUB


SUB fn1 (a1,b1)
	PRINT "calling fnn(a1)"
	CALL fnn(a1)
	PRINT "fn1 called"
	RETURN ""
ENDSUB

SUB fnn(a)
	print "called sub 2"
	RETURN ""
ENDSUB

SUB multiply(x, y)
	PRINT x * test(y)
	LET blubb = 33
ENDSUB
LABEL 100000
