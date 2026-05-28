# PowerShell script to add firewall rules for Laravel development
# Run this as Administrator

Write-Host "Adding firewall rules for Laravel development server..." -ForegroundColor Yellow

# Remove existing rules if they exist
Remove-NetFirewallRule -DisplayName "Laravel PHP Server" -ErrorAction SilentlyContinue
Remove-NetFirewallRule -DisplayName "Laravel Vite Server" -ErrorAction SilentlyContinue

# Add rule for PHP server (port 8000)
New-NetFirewallRule -DisplayName "Laravel PHP Server" -Direction Inbound -LocalPort 8000 -Protocol TCP -Action Allow -Profile Any
Write-Host "Added firewall rule for PHP server on port 8000" -ForegroundColor Green

# Add rule for Vite server (port 5173)
New-NetFirewallRule -DisplayName "Laravel Vite Server" -Direction Inbound -LocalPort 5173 -Protocol TCP -Action Allow -Profile Any
Write-Host "Added firewall rule for Vite server on port 5173" -ForegroundColor Green

Write-Host "`nFirewall rules added successfully!" -ForegroundColor Green
Write-Host "You can now access the server from your LAN." -ForegroundColor Green
