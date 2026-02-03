@echo off
title UniJob - Server
cd /d "%~dp0"

echo.
echo  Starting UniJob...
echo  Website will be at:  http://localhost:8000
echo  Press Ctrl+C or close this window to stop the server.
echo.

start /B cmd /c "timeout /t 2 /nobreak >nul && start http://localhost:8000"
php artisan serve

pause
