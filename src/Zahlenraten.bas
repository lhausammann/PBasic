LET normalFG = 1
LET normalBG = 15
LET errorFG = 4
LET errorBG = 14

LABEL 3
COLOR normalFG, normalBG

LABEL 1

PRINT "Ich denke mir eine Zahl zwischen eins und zehn aus"
LET rnd = RND(1,10)

LABEL 20
INPUT "IHRE SCHAETZUNG? ", a



IF (HALLO(a)=false) THEN
	PRINT "Eingabe muss zwischen 1 und 10 sein."
	GOTO 1
ENDIF


IF a = rnd THEN
	
	PRINT "GRATULATION! SIE HABEN DIE ZAHL ERRATEN!"
	
	INPUT "NOCHMALS SPIELEN (y|n)", n
	IF n="y" THEN
		GOTO 1
	ELSE
		PRINT "ES HAT GROSSEN SPASS GEMACHT. AUF WIEDERSEHEN"
		END
	ENDIF
ENDIF

IF (a < rnd) THEN
	PRINT rnd
	CALL PRINTWRONG ("MEINE AUSGEDACHTE ZAHL IST GROESSER ALS ", a, errorFG, errorBG, normalFG, normalBG)
	GOTO 20
ELSE
	PRINT rnd
	CALL PRINTWRONG("MEINE AUSGEDACHTE ZAHL IST KLEINER ALS ", a, errorFG, errorBG, normalFG, normalBG)
ENDIF
GOTO 20


END



SUB HALLO(i)
	IF ((i > 0) AND (i < 11)) THEN
		RETURN 1
	ELSE 
		RETURN 0
	ENDIF
ENDSUB

SUB ENTERNAME
	PRINT "WIE IST DEIN NAME?"
	INPUT name
	PRINT "HALLO", name, "!!!"
	PRINT "after return should not happen"
	RETURN
ENDSUB

SUB GETNAME
	PRINT "HIER"
	RETURN "HELLO UNKNOWN"
	PRINT "SHOULD NOT HAPPEN"
ENDSUB

SUB PRINTWRONG(msg, zahl, errorFG, errorBG, normalFG, normalBG)
	COLOR errorFG, errorBG
	PRINT msg, " ", zahl
	COLOR normalFG, normalBG
	RETURN
	PRINT "SKIPPED"
ENDSUB





