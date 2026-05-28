@echo off
echo Starting PrintSync development server...
echo.
echo IMPORTANT: If you can't access from LAN, run this command as Administrator:
echo   powershell -ExecutionPolicy Bypass -File add-firewall-rules.ps1
echo.
echo Starting PHP artisan server on port 8000 (LAN accessible)...
start "PHP Server" cmd /k "php artisan serve --host=0.0.0.0 --port=8000"
echo.
echo Starting npm run dev...
start "Vite Dev Server" cmd /k "npm run dev"
echo.
echo Both servers are starting in separate windows.
echo.
echo Access URLs:
echo   Local:  http://localhost:8000
echo   LAN:    http://0.0.0.0:8000
echo.
echo Make sure your phone is on the same WiFi network (192.168.1.x)
echo.
echo Press any key to close this window...
pause >nul
