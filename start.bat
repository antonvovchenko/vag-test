ECHO OFF
cd /d %~dp0
for /f "tokens=2* delims= " %%F IN ('vagrant status ^| find /I "default"') DO (SET "STATE=%%F%%G")

ECHO Processing...

set somethingrun=0

IF "%STATE%"=="running" (
  ECHO Vagrant running. Execute provision...
  set somethingrun=1
  vagrant provision
)

IF "%STATE%"=="saved" (
  ECHO Resuming Vagrant VM from saved state...
  set somethingrun=1
  vagrant resume
)

IF %test1result%==0 (
  ECHO Starting Vagrant VM...
  vagrant up
)

if errorlevel 1 (
  ECHO FAILURE! Vagrant VM unresponsive...
)

set /p=Hit ENTER to finish.