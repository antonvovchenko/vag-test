ECHO OFF
cd /d %~dp0

ECHO Destroy Vagrant VM...
vagrant destroy

if errorlevel 1 (
  ECHO FAILURE! Vagrant VM unresponsive...
)


set /p=Hit ENTER to finish.