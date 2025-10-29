@echo off
REM Deployment Cache Clear Script (Windows)
REM Run this script after deployment to ensure all caches are cleared

echo === Laravel Cache Clear Script ===
echo.

REM Clear all caches
echo 1. Clearing configuration cache...
php artisan config:clear

echo 2. Clearing route cache...
php artisan route:clear

echo 3. Clearing view cache...
php artisan view:clear

echo 4. Clearing application cache...
php artisan cache:clear

echo 5. Clearing compiled files...
php artisan clear-compiled

echo 6. Clearing event cache...
php artisan event:clear

echo.
echo === Optimizing for Production ===
echo.

REM Optimize for production
echo 7. Caching configuration...
php artisan config:cache

echo 8. Caching routes...
php artisan route:cache

echo 9. Caching views...
php artisan view:cache

echo.
echo === Livewire Assets ===
echo.

REM Republish Livewire assets
echo 10. Publishing Livewire assets...
php artisan vendor:publish --tag=livewire:assets --force

echo 11. Publishing Livewire Tables assets...
php artisan vendor:publish --tag=livewire-tables --force

echo.
echo === Database Check ===
echo.

REM Check database connection
echo 12. Testing database connection...
php artisan db:show

echo.
echo === Complete! ===
echo.
echo Don't forget to:
echo 1. Clear browser cache (Ctrl+Shift+Delete)
echo 2. Check JavaScript console for errors (F12)
echo 3. Test search functionality
echo.

pause
