@echo off
echo === Customer Portal Local Setup ===
echo.

:: Check if composer is installed
where composer >nul 2>nul
if %errorlevel% neq 0 (
    echo ❌ Composer is not installed. Please install Composer first.
    echo    Visit: https://getcomposer.org/download/
    pause
    exit /b 1
)

:: Check if PHP is installed
where php >nul 2>nul
if %errorlevel% neq 0 (
    echo ❌ PHP is not installed. Please install PHP 8.2+ first.
    pause
    exit /b 1
)

echo ✅ PHP and Composer detected

:: Install dependencies
echo.
echo 📦 Installing PHP dependencies...
composer install --no-dev --optimize-autoloader

if %errorlevel% neq 0 (
    echo ❌ Failed to install dependencies
    pause
    exit /b 1
)

:: Copy environment file
if not exist .env (
    echo.
    echo 📝 Creating environment file...
    copy .env.example .env
    echo ✅ Created .env file
) else (
    echo ⚠️  .env file already exists
)

:: Generate application key
echo.
echo 🔑 Generating application key...
php artisan key:generate

:: Database setup
echo.
echo 🗄️  Database setup...
set /p DB_SETUP="Do you have MySQL/MariaDB running locally? (y/n): "
if /i "%DB_SETUP%"=="y" (
    set /p DB_NAME="Database name (default: customer_portal): "
    if "%DB_NAME%"=="" set DB_NAME=customer_portal
    
    set /p DB_USER="Database username (default: root): "
    if "%DB_USER%"=="" set DB_USER=root
    
    set /p DB_PASS="Database password: "
    
    :: Update .env file (basic approach for Windows)
    echo Updating .env file...
    powershell -Command "(Get-Content .env) -replace 'DB_DATABASE=.*', 'DB_DATABASE=%DB_NAME%' | Set-Content .env"
    powershell -Command "(Get-Content .env) -replace 'DB_USERNAME=.*', 'DB_USERNAME=%DB_USER%' | Set-Content .env"
    powershell -Command "(Get-Content .env) -replace 'DB_PASSWORD=.*', 'DB_PASSWORD=%DB_PASS%' | Set-Content .env"
    
    echo ✅ Database configuration updated
    
    :: Run migrations
    echo 🔄 Running database migrations...
    php artisan migrate --force
    
    if %errorlevel% equ 0 (
        echo ✅ Database migrations completed
    ) else (
        echo ⚠️  Database migrations failed - check your database connection
    )
) else (
    echo ⚠️  Please set up MySQL/MariaDB and update the .env file manually
)

:: Clear caches
echo.
echo 🧹 Clearing caches...
php artisan config:clear
php artisan cache:clear
php artisan view:clear

echo.
echo 🎉 Setup complete!
echo.
echo To start the development server:
echo   php artisan serve
echo.
echo Then visit:
echo   - Main app: http://localhost:8000
echo   - Markdown Editor: http://localhost:8000/markdowneditor
echo   - Test route: http://localhost:8000/test-route
echo.
echo To test the markdown editor:
echo   1. Add class 'markdown-editor' to any textarea
echo   2. Include the plugin CSS and JS files
echo   3. The textarea will be automatically enhanced
echo.
echo Plugin files are located in:
echo   - plugins/markdowneditor/
echo.
echo Happy coding! 🚀
echo.
pause
