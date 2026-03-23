Write-Host "======================================" -ForegroundColor Cyan
Write-Host "JM-EMS Installation Script" -ForegroundColor Cyan
Write-Host "Joan-Mat Enterprise Management System" -ForegroundColor Cyan
Write-Host "======================================" -ForegroundColor Cyan
Write-Host ""

# Check PHP version
Write-Host "Checking PHP version..." -ForegroundColor Yellow
try {
    $phpVersion = php -v 2>&1 | Select-String "PHP ([0-9.]+)"
    if ($phpVersion -match "PHP 8\.[0-9.]+") {
        Write-Host "✓ PHP version OK: $($Matches[1])" -ForegroundColor Green
    } else {
        Write-Host "⚠ PHP version may be below 8.0" -ForegroundColor Yellow
    }
} catch {
    Write-Host "⚠ Could not determine PHP version" -ForegroundColor Yellow
}

# Check Composer
Write-Host "Checking Composer..." -ForegroundColor Yellow
$composer = Get-Command composer -ErrorAction SilentlyContinue
if ($composer) {
    Write-Host "✓ Composer found" -ForegroundColor Green
} else {
    Write-Host "✗ Composer not found. Please install Composer first." -ForegroundColor Red
    exit
}

# Copy .env file
if (!(Test-Path ".env")) {
    Write-Host "Creating .env file..." -ForegroundColor Yellow
    Copy-Item ".env.example" ".env"
}

# Install dependencies
Write-Host "Installing Composer dependencies..." -ForegroundColor Yellow
composer install --no-interaction

# Generate key
Write-Host "Generating application key..." -ForegroundColor Yellow
php artisan key:generate --no-interaction

# Run migrations
Write-Host "Running migrations..." -ForegroundColor Yellow
php artisan migrate:fresh --seed --no-interaction

# Create storage link
Write-Host "Creating storage link..." -ForegroundColor Yellow
php artisan storage:link --no-interaction

# Optimize
Write-Host "Optimizing application..." -ForegroundColor Yellow
php artisan optimize --no-interaction

# Verify installation
Write-Host "Verifying installation..." -ForegroundColor Yellow
php artisan jm-ems:verify --no-interaction

Write-Host ""
Write-Host "======================================" -ForegroundColor Green
Write-Host "Installation Complete!" -ForegroundColor Green
Write-Host "======================================" -ForegroundColor Green
Write-Host ""
Write-Host "To start the application, run:" -ForegroundColor Yellow
Write-Host "php artisan serve" -ForegroundColor Cyan
Write-Host ""
Write-Host "Login Credentials:" -ForegroundColor Yellow
Write-Host "Admin: admin@jm-ems.com / password" -ForegroundColor Cyan
Write-Host "Attendant: attendant@jm-ems.com / password" -ForegroundColor Cyan
Write-Host "Manager: manager@jm-ems.com / password" -ForegroundColor Cyan
Write-Host ""
Read-Host "Press Enter to exit"