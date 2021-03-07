@SET "HUMBLE_HOME=%cd%"
@IF "%HUMBLE_HOME%" == "" GOTO NOPATH
@php Humble.php %*
@GOTO END
:NOPATH
@echo The environment variable HUMBLE_HOME is not set, please set it to the root directory of your application before trying again
:END