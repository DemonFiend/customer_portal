# Local Development Setup Guide

## Prerequisites

1. **PHP 8.2+** with extensions:
   - OpenSSL
   - PDO
   - Mbstring
   - Tokenizer
   - XML
   - Ctype
   - JSON
   - BCMath

2. **Composer** - PHP dependency manager
3. **Node.js & NPM** - For frontend assets
4. **MySQL/MariaDB** - Database
5. **Web Server** - Apache/Nginx (or use Laravel's built-in server)

## Windows Setup Instructions

### 1. Install PHP
Download PHP 8.2+ from [php.net](https://www.php.net/downloads.php) or use tools like:
- **XAMPP** (includes Apache, MySQL, PHP)
- **Laragon** (lightweight alternative)
- **WAMP** (Windows, Apache, MySQL, PHP)

### 2. Install Composer
Download from [getcomposer.org](https://getcomposer.org/download/)

### 3. Install Node.js
Download from [nodejs.org](https://nodejs.org/)

### 4. Setup Database
If using XAMPP/WAMP, MySQL is included. Otherwise install MySQL/MariaDB separately.

## Project Setup Steps

### 1. Install Dependencies
```bash
# Install PHP dependencies
composer install

# Install Node dependencies (if package.json exists)
npm install
```

### 2. Environment Configuration
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 3. Configure .env file
Edit the `.env` file with your settings:

```env
# Application
APP_NAME="Customer Portal"
APP_ENV=local
APP_KEY=base64:YOUR_GENERATED_KEY
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=customer_portal
DB_USERNAME=root
DB_PASSWORD=your_password

# Sonar API (for testing, use demo credentials)
API_USERNAME=admin
API_PASSWORD=demo
SONAR_URL=https://demo.sonar.software

# Mail (for testing, use log driver)
MAIL_MAILER=log
```

### 4. Database Setup
```bash
# Create database
mysql -u root -p -e "CREATE DATABASE customer_portal;"

# Run migrations
php artisan migrate

# (Optional) Seed database with test data
php artisan db:seed
```

### 5. Build Assets (if needed)
```bash
# Compile frontend assets
npm run dev

# Or for production
npm run build
```

### 6. Start Development Server
```bash
# Laravel's built-in server
php artisan serve

# Or specify port
php artisan serve --port=8080
```

Your application will be available at: http://localhost:8000

## Plugin Development Mode

To enable plugin development features, add to your `.env`:

```env
PLUGIN_DEBUG=true
PLUGIN_HOT_RELOAD=true
PLUGIN_SHOW_ERRORS=true
PLUGIN_CACHE_ENABLED=false
```

## Testing the Markdown Editor

Once setup is complete:

1. Navigate to `/markdowneditor` to see the plugin
2. Test markdown rendering in forms
3. Check plugin management at `/admin/plugins`

## Troubleshooting

### Common Issues:

1. **Composer not found**: Add PHP and Composer to your system PATH
2. **Database connection failed**: Check MySQL is running and credentials are correct
3. **Permission errors**: On Windows, run command prompt as Administrator
4. **Laravel key not set**: Run `php artisan key:generate`
5. **Plugin not loading**: Check `storage/logs/laravel.log` for errors

### Logs Location:
- Laravel logs: `storage/logs/laravel.log`
- Plugin logs: Check Laravel logs for plugin-specific errors

## Development Workflow

1. Make changes to plugin files
2. If `PLUGIN_HOT_RELOAD=true`, changes are picked up automatically
3. Otherwise, clear cache: `php artisan cache:clear`
4. Test functionality in browser
5. Check logs for any errors
