@IF "%JARVIS_HOME%" == "" GOTO NOPATH
@php %JARVIS_HOME%\Harness.php %*
@GOTO END
:NOPATH
@echo The environment variable JARVIS_HOME is not set, please set it before trying again
:END