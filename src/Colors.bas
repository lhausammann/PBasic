LET a = 10
LET x = 9
LET b = 5

FOR zz=0 TO 10
NEXT
PRINT zz

PRINT zz

WHILE a > 8
	a = a -1
	b = 8
	PRINT "OUTER", a
	WHILE b < 10
		INPUT z
		PRINT z
		PRINT "INNER", b
		b = b + 1
		PRINT "a * b = ", a, "*", b, "=", a * b
	WEND
WEND

PRINT "before for"

FOR i = 1 TO 3 STEP 1
	PRINT "First..."
	PRINT "Second.."
	PRINT "Third"
	PRINT "Fourth"
	PRINT "Fifth"
	PRINT "Sixth"
	PRINT "Seventh"
	PRINT "Eighth"
	PRINT "Ninth"
	PRINT "TENTH"
	PRINT i
	PRINT "CONTINUE"
NEXT i


PRINT "check break"

FOR i = 1 TO 3 STEP 1
	PRINT "First"
	PRINT "Second"
	PRINT "Third"
	PRINT "Fourth"
	PRINT "Fifth"
	PRINT "Sixth"
	PRINT "Seventh"
	PRINT "Eighth"
	PRINT "Ninth"
	PRINT "TENTH"
	PRINT i
	PRINT "CONTINUE"
NEXT i

PRINT "DONE"



FOR background = 1 TO 16
	PRINT "OUTER, starting INNER"
	FOR foreground = 1 TO 16
		COLOR foreground, background
		PRINT "HI", foreground, background
		PRINT ""
	NEXT foreground
	PRINT "TERMINATED INNER LOOP"
NEXT background

PRINT "END"
END