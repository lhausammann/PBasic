PRINT "Enter a positive or negative number: "
INPUT number
PRINT "Enter your first name: "
INPUT firstName$
PRINT "Enter your last name: "
INPUT lastName$

PRINT
PRINT "YOUR INPUT"
PRINT "----------------------------------------"
PRINT "     number = ", number
PRINT "     First Name: ", firstName$
PRINT "     Last Name: ", lastName$
PRINT "The absolute value of number is: ", ABS(number)

let ranNum = INT((RND() * (number + 1) + 0))
SCOPEDUMP
PRINT "A randon integer from 0 to ", number, "is: ", ranNum

IF (SGN(number)) = 1 THEN
   PRINT "number is positive."
   PRINT "The square root of number is: ", SQR(number)
ELSE
   PRINT "number is neutral (0)."
ENDIF
