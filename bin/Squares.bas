 
10 SCOPEDUMP 

print "Table of Squares"
 print
 print "How many values would you like?"
 input num
 for i=1 to num
 print i, i*i
 next i

CALL MORE = m
PRINT m

if m = 1 then
	GOTO 10
endif

1000 end

SUB MORE
	INPUT a
	PRINT a
	IF a="y" THEN
		GOTO 10
	ENDIF
	PRINT "BLAH"
	RETURN 33
ENDSUB