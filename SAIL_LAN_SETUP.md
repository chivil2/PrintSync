# Laravel Sail LAN Setup Instructions

## 1. Configure .env for LAN Access

Update your `.env` file with the following changes:

### Database Configuration (SQLite)
```
DB_CONNECTION=sqlite
```

Ensure `database/database.sqlite` exists. If not, create it:
```bash
touch database/database.sqlite
```

### LAN Access Configuration
To access the app from other devices on your LAN:

1. Find your server's local IP address (e.g., `192.168.1.100`)
2. Update `APP_URL` in `.env`:
   ```
   APP_URL=http://192.168.1.100:80
   ```
3. If using Sanctum for API authentication, add your LAN IP to stateful domains:
   ```
   SANCTUM_STATEFUL_DOMAINS=192.168.1.100:80
   ```

### Sail Configuration
Add these to your `.env`:
```
WWWGROUP=1000
WWWUSER=1000
APP_PORT=80
VITE_PORT=5173
SAIL_XDEBUG_MODE=off
SAIL_XDEBUG_CONFIG=client_host=host.docker.internal
```

## 2. Start Sail

Run the following commands:

```bash
# Build and start containers
./vendor/bin/sail up

# Or run in the background
./vendor/bin/sail up -d
```

## 3. Run Migrations

```bash
./vendor/bin/sail artisan migrate
```

## 4. Access the Application

- **Local access**: http://localhost
- **LAN access**: http://YOUR_SERVER_IP (e.g., http://192.168.1.100)

## 5. Useful Sail Commands

```bash
# Stop containers
./vendor/bin/sail stop

# View logs
./vendor/bin/sail logs

# Run artisan commands
./vendor/bin/sail artisan [command]

# Run npm commands
./vendor/bin/sail npm [command]

# Access container shell
./vendor/bin/sail shell
```

## 6. Services Available

- **Application**: Port 80
