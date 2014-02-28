x = 0
success = true

10 REM check GOTOs
x = x + 1
IF x = 5 THEN
    GOTO 30
    success = 33
    REM this must never happen
    success = false

ENDIF

20 GOTO 10
success = false
30 GOTO 1000
success = false

label 1000


x = 0


1010 REM check GOTOs
x = x + 1
IF x = 5 THEN
    GOTO 3030
    success = 33
    REM this must never happen
    success = false
ELSE
    GOTO 1010
ENDIF

2020 GOTO 1010
success = false
3030 GOTO 10001000
success = false

label 10001000

if (5 = 5) THEN 11111111
success = false

11111111
