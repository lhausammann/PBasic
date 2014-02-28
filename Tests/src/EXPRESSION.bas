value = 5
a = 7 * 5 + 3 - (2 - 2 / 2 + 1)
success1 = (a = 36)
success2 = double$(a) = a * 2
success3 = (a * 10 = 360)
success4 = value = 5
success5 = a / 2 = 18


myFalse = !true
myTrue = !!true
isParentThesisApplied = (5+1) * 3 = 18
isFiveLowerEight = 5 < 8
isFiveLowerEqualsEight = 5 <= 8

isFiveGreaterFour = 5 > 4
isFiveGreaterEqualsFour = 5 >= 4

isFiveGreaterEqualsFive = 5 >= 5
isFiveLowerEqualsFive = 5 <= 5
isFiveEqualsFive = 5 = 5
positive = -1 * -1

isFloat = 1.2 * 10 = 12


isPunktVorStrich = 3 + 5 * 3 = 18
areParenthesisApplied = (3 +5) * 3 = 24

isCorrectPrecedenceORBeforeEquals = (3 = 3 AND 5 * 5= 25 )

uppercaseVar = strtoupper("hello test")
isUpperCaseTest = ("HELLO TEST" = uppercaseVar)


SUB double$                          (value)
    RETURN  value *                         2
    REM Must not happen
    END
ENDSUB