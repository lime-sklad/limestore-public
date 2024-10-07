
@echo off
color a
title Lime Store Launcher v0.6
set EXEC_CMD="xampp-control.exe"
wmic process where (name=%EXEC_CMD%) get commandline | findstr /i %EXEC_CMD%> NUL
if errorlevel 1 (
   Start "" "e:\emil\xampp\xampp-control.exe"
   Start "" E:\Emil\xampp\htdocs\remote-server\launch-remote-server.vbs"
   cls
   timeout /t 5
   start chrome localhost
   exit
) else (
   cls
   timeout /t 3
   start chrome localhost
   exit
)