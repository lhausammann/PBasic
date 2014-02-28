globalA = 100

100 PRINT "HALLO"

200 GOSUB 1000

PRINT "global a ist ", globalA


END

1000
globalA = globalA * 2
PRINT globalA
RETURN




