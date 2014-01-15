


10 COLOR 7,0
INPUT "WIE VIELE STERNE MOECHTEST DU?", x
LET rnd = 5

FOR j = 1 TO x
	LET rand = RND(0,12)
	CALL tab(rand, "*") = star
	' PRINT rand
	COLOR rand, rand
	PRINT star
	
NEXT j


INPUT "Noch mehr (j/n)", a
IF a = "j" THEN
	GOTO 10
ENDIF

SUB tab(n, inp)
	n = n-2
	size = 6
	LET i = 0
	FOR i = 1 TO (n * size) 
		inp = "&nbsp;" + inp
	NEXT i
	RETURN inp
ENDSUB