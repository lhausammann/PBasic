PRINT "First"
PRINT "second"
READ cc
IF cc > 8 THEN 
	END
ENDIF
globalA = 100
c = 0
100 PRINT "START"

PRINT "Start 2"
FOR i=1 to 5
	GOSUB 1000

	10 PRINT "AFTER GOSUB"
	PRINT "i ist", i
NEXT i

PRINT "End of loop."

END

1000
	PRINT "HALLO GOSUB"
RETURN

PRINT "Must not be here."

DATA 1,2,4,8,16


