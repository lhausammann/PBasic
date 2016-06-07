PRINT "First"
PRINT "second"

globalA = 100
c = 0
100 PRINT "START"

PRINT "Start 2"
FOR i=1 to 5
	print "da"
	GOSUB 1000
	PRINT "r ist ", r
	PRINT "AFTER GOSUB"
	PRINT "i ist", i

NEXT i

PRINT "End of loop."

END

1000
	PRINT "HALLO GOSUB"
	READ r
	PRINT "Gosub r ist", r
RETURN

PRINT "Must not be here."

DATA 1,2,4,8,16


