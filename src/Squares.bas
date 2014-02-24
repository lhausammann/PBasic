print "Table of Squares"
 print "--------------------------------"
 print "How many values would you like?"
 print "--------------------------------"
 input num
 for i=1 to num
 print i, i*i
 next i

CALL MORE = m

if m = true then
	GOTO 10
endif

1000 end

SUB MORE
    COLOR 3,8
    PRINT "AGAIN? Y/N"
    INPUT a
    RETURN ucfirst(a)="Y"
ENDSUB