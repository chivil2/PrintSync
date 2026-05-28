# Windows LAN Setup (Without Docker/WSL)

Since Laravel Sail requires WSL/Docker which isn't installed, use this alternative for LAN access on Windows.

## 1. Configure .env for LAN Access

Update your `.env` file:

### Database Configuration (SQLite)
```
DB_CONNECTION=sqlite
```

Ensure `database/database.sqlite` exists. If not, create it:
```powershell
New-Item -Path database\database.sqlite -ItemType File -Force
```

### LAN Access Configuration
To access the app from other devices on your LAN:

1. Find your server's local IP address:
   ```powershell
   ipconfig
   ```
   Look for "IPv4 Address" (e.g., `192.168.1.100`)

2. Update `APP_URL` in `.env`:
   ```
   APP_URL=http://192.168.1.100:8000
   ```

3. If using Sanctum for API authentication, add your LAN IP to stateful domains:
   ```
   SANCTUM_STATEFUL_DOMAINS=192.168.1.100:8000
   ```

## 2. Configure Windows Firewall

Allow PHP/Laravel through Windows Firewall:

```powershell
# Allow PHP on port 8000
New-NetFirewallRule -DisplayName "Laravel Development Server" -Direction Inbound -LocalPort 8000 -Protocol TCP -Action Allow
```

Or manually:
- Open Windows Defender Firewall
- Click "Allow an app or feature through Windows Defender Firewall"
- Allow "PHP" or add a custom rule for port 8000

## 3. Start the Development Server

### Option A: Using Artisan Serve
```powershell
php artisan serve --host=0.0.0.0 --port=8000
```

### Option B: Using the start.bat
Update `start.bat` to bind to all network interfaces:

```batch
@echo off
php artisan serve --host=0.0.0.0 --port=8000
```

Then run:
```powershell
.\start.bat
```

## 4. Access the Application

- **Local access**: http://localhost:8000
- **LAN access**: http://YOUR_SERVER_IP:8000 (e.g., http://192.168.1.100:8000)

## 5. Run Migrations

```powershell
php artisan migrate
```

## 6. Run Vite for Frontend Development

In a separate terminal:
```powershell
npm run dev
```

## 7. Useful Commands

```powershell
# Stop server: Ctrl+C

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Run tests
php artisan test

# Run npm commands
npm run build
npm run dev
```

## 8. Troubleshooting

### Can't access from other devices:
1. Check Windows Firewall is allowing port 8000
2. Verify your IP address with `ipconfig`
3. Ensure the server is running with `--host=0.0.0.0`
4. Try disabling antivirus temporarily to test

### Port already in use:
Change the port in your command:
```powershell
php artisan serve --host=0.0.0.0 --port=8080
```
Then update `APP_URL` in `.env` to match.

### Database issues:
Ensure `database/database.sqlite` exists and has write permissions.
