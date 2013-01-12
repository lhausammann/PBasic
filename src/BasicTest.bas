PRINT "Start"
PRINT "Wie viele Sterne?"
INPUT nr
FOR i = 1 TO nr
	PRINT "*"
NEXT

LET a = 10




Input blah
PRINT "a ist"
PRINT a
PRINT "nr ist:"
PRINT nr


FOR i = 100 TO 200 STEP 100
 PRINT i

 FOR j = 1 TO 3
 	PRINT "i * j ist:"
 	
 	PRINT i * j
 	
 NEXT
NEXT
LET counter = 1

PRINT "Vor While"
WHILE (counter < 10)
	INPUT k
	PRINT "exe while"
	LET counter = counter + 1
	PRINT "counter ist:"
	PRINT counter
WEND

print "Hello, World!"
COLOR 3,5
PRINT "Wie geht es so?"
Let debug = 1
LET a = 7
PRINT a

IF a = 5 THEN 
	PRINT "glt 5"
ELSE
	COLOR 4,5
	PRINT "a grösser 6"
ENDIF

' TEST
LABEL 10
PRINT "Reached label 10"
LET a = a * 3
PRINT a
IF (a > 100) THEN
	IF (debug=1) THEN
		END
	ELSE
		GOTO 1000
	ENDIF
ENDIF
PRINT "after check"
PRINT a
GOTO 10
label 1000
PRINT "DEBUG NOT SET"

