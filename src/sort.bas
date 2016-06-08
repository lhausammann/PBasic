
INPUT "How many elements to sort?", elems
DIM MyArray(elems)

FOR i=0 TO elems
	INPUT z
	LET MyArray(i) = z
NEXT


FOR i = 0 TO elems
	FOR j = 0 TO elems 
		if (MyArray(i) < MyArray(j)) THEN
			z = MyArray(i)
			MyArray(i) = MyArray(j)
			MyArray(j) = z
		ENDIF
	NEXT j
NEXT i

FOR i=0 TO elems
	PRINT MyArray(i)
NEXT




