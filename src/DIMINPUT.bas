INPUT "Wie viele Zahlen", max
DIM arr(max)

FOR i = 0 TO max-1
	INPUT zahl
	arr(i) = zahl
NEXT

FOR i = max-1 to 0 STEP -1
	PRINT "Eingabe war:", arr(i)
NEXT