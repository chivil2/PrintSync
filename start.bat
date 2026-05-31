@echo off
echo Starting PrintSync development server...
echo.
echo IMPORTANT: If you can't access from LAN, run this command as Administrator:
echo   powershell -ExecutionPolicy Bypass -File add-firewall-rules.ps1
echo.
echo Starting PHP artisan server on port 8000 (LAN accessible)...
start "PHP Server" cmd /k "php artisan serve --host=0.0.0.0 --port=8000"
echo.
echo Starting Mailpit (email catcher)...
start "Mailpit" cmd /k "mailpit"
echo.
echo Starting npm run dev...
start "Vite Dev Server" cmd /k "npm run dev"
echo.
echo Both servers are starting in separate windows.
echo.
echo Access URLs:
echo   Local:    http://localhost:8000
echo   LAN App:  http://192.168.1.x:8000  (replace x with your PC's IP)
echo   Mailpit:  http://localhost:8025
echo.
echo Make sure other devices are on the same WiFi network
echo.
echo Press any key to close this window...
pause >nul
