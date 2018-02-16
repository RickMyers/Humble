@SET "JARVIS_HOME=%cd%"
@IF "%JARVIS_HOME%" == "" GOTO NOPATH
@php Humble.php %*
@GOTO END
:NOPATH
@echo The environment variable JARVIS_HOME is not set, please set it to the root directory of your application before trying again
:END