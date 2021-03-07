@IF "%HUMBLE_HOME%" == "" GOTO NOPATH
@php %HUMBLE_HOME%\Harness.php %*
@GOTO END
:NOPATH
@echo The environment variable HUMBLE_HOME is not set, please set it before trying again
:END