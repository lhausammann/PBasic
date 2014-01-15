PRINT "Test subs"

CALL COUNT(10, 0)
PRINT "END COUNTING"
CALL COUNT(5, 1)
PRINT "END COUNTING 2"
PRINT COUNT(10, 2)
PRINT "END COUNT"
END


SUB COUNT(from, withInput)
	
	PRINT "start count"
	'LET i = 0
	FOR i = 1 TO from
		'INPUT z
		print i
	
	
		PRINT from
		'LET from = from -1
	NEXT i
	PRINT "END"
	RETURN 1
ENDSUB