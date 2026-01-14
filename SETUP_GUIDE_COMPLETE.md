# Customer Portal - Complete Setup Guide

This guide covers both the **default portal** setup and the **plugin-enhanced version** for local development and production deployment.

---

## Table of Contents

1. [Default Portal Setup (Production)](#1-default-portal-setup-production)
2. [Plugin-Enhanced Portal Setup (Local Development)](#2-plugin-enhanced-portal-setup-local-development)
3. [Dark Mode Plugin Proof of Concept](#3-dark-mode-plugin-proof-of-concept)
4. [Testing & Verification](#4-testing--verification)
5. [Troubleshooting](#5-troubleshooting)

---

## 1. Default Portal Setup (Production)

This is the standard setup for deploying the customer portal on a server using Docker.

### 1.1 Prerequisites

- **Ubuntu 18/22 x64** (or compatible Debian-based distro)
- **2+ vCPUs, 2+ GB RAM, 25+ GB storage**
- **Public IP address**
- **Domain name** pointing to the server (e.g., portal.example.com)
- **Root or sudo access**

### 1.2 Initial Server Setup

```bash
# 1. Update system
sudo apt-get -y update && sudo apt-get -y upgrade

# 2. Install required packages
sudo apt-get -y install git unzip

# 3. Clone repository
git clone https://github.com/SonarSoftwareInc/customer_portal.git
cd customer_portal
```

### 1.3 Run Installation Script

```bash
# Run the automated installer
sudo ./install.sh | tee customerportal-install.log
```

The installer will:
- Install Docker and Docker Compose
- Set up Nginx with SSL (LetsEncrypt)
- Configure MySQL database
- Set up automatic updates (Watchtower)
- Configure the application

### 1.4 Installation Prompts

You'll be asked for:

| Prompt | Example | Notes |
|--------|---------|-------|
| Domain name | portal.myisp.com | Must have DNS pointing to server |
| Email address | admin@myisp.com | For LetsEncrypt SSL certificate |
| API Username | customerportal | Create in Sonar first |
| API Password | [secure_password] | Don't use ^ or \ characters |
| Sonar URL | https://yourinstance.sonar.software | Your Sonar instance URL |

### 1.5 Sonar Configuration

#### For Sonar V2 (Current):

1. Go to **System → API Users** in Sonar
2. Create new API user: "Customer Portal"
3. Assign permissions:
   - Account Management: Read, Create, Update
   - Billing: Read
   - Ticketing: Read, Create, Update, Delete
   - Data Usage: Read

See [Sonar Knowledge Base](https://docs.sonar.expert/baseline-config/customer-portal-configuration-checklist#api_user_permissions) for details.

#### For Sonar V1 (Legacy):

1. Go to **System → Roles**
2. Create role: "Customer Portal"
3. Assign permissions:
   - Accounts: Read, Create, Update, Delete
   - Financial: Read
   - Ticketing: Read, Create, Update, Delete
   - Ticket Super User: Enabled

### 1.6 Post-Installation

```bash
# View logs
sudo docker-compose logs -f

# Check status
sudo docker-compose ps

# Access settings page
# Navigate to: https://portal.myisp.com/settings
# Use the settings key generated during installation
```

### 1.7 Common Docker Commands

```bash
# Start portal
sudo docker-compose start

# Stop portal
sudo docker-compose stop

# Restart portal
sudo docker-compose restart

# View logs
sudo docker-compose logs

# Access container shell
sudo docker-compose exec app /bin/bash

# Update portal (automatic via Watchtower)
# Manual update: sudo docker-compose pull && sudo docker-compose up -d
```

### 1.8 Useful Artisan Commands (Inside Container)

```bash
# Enter container
sudo docker-compose exec app /bin/bash

# Generate new settings key
php artisan sonar:settingskey

# Test email configuration
php artisan sonar:test:smtp your@email.com

# Test PayPal configuration
php artisan sonar:test:paypal

# Exit container
exit
```

---

## 2. Plugin-Enhanced Portal Setup (Local Development)

This setup is for developing and testing plugins locally without Docker.

### 2.1 Prerequisites

#### Required Software:

1. **PHP 8.2+** with extensions:
   - OpenSSL, PDO, Mbstring, Tokenizer, XML
   - Ctype, JSON, BCMath, Fileinfo, GD

2. **Composer** - [Download](https://getcomposer.org/)
3. **Node.js 16+** & NPM - [Download](https://nodejs.org/)
4. **MySQL 5.7+** or **MariaDB 10.3+**
5. **Git**

#### Quick Install (Windows - Using Laragon):

```bash
# Download Laragon Full: https://laragon.org/download/
# Includes PHP, MySQL, Node.js, Composer, Git
# Extract and run Laragon.exe
```

#### Quick Install (Mac - Using Homebrew):

```bash
# Install Homebrew: https://brew.sh/

# Install PHP
brew install php@8.2

# Install Composer
brew install composer

# Install MySQL
brew install mysql

# Install Node.js
brew install node
```

#### Quick Install (Linux - Ubuntu/Debian):

```bash
# Add PHP repository
sudo add-apt-repository ppa:ondrej/php
sudo apt-get update

# Install PHP 8.2 and extensions
sudo apt-get install -y php8.2 php8.2-cli php8.2-fpm php8.2-mysql \
  php8.2-xml php8.2-mbstring php8.2-curl php8.2-zip php8.2-gd \
  php8.2-bcmath php8.2-intl

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install MySQL
sudo apt-get install -y mysql-server

# Install Node.js
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt-get install -y nodejs
```

### 2.2 Clone and Setup Project

```bash
# 1. Clone repository
git clone https://github.com/SonarSoftwareInc/customer_portal.git
cd customer_portal

# 2. Install PHP dependencies
composer install

# 3. Install Node dependencies
npm install

# 4. Create environment file
cp .env.example .env

# 5. Generate application key
php artisan key:generate
```

### 2.3 Configure Environment

Edit `.env` file:

```env
# Application
APP_NAME="Customer Portal"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=customer_portal_dev
DB_USERNAME=root
DB_PASSWORD=your_mysql_password

# Sonar API (use demo for testing)
API_USERNAME=demo
API_PASSWORD=demo
SONAR_URL=https://demo.sonar.software

# Mail (log emails instead of sending)
MAIL_MAILER=log
MAIL_FROM_ADDRESS=noreply@customerportal.local
MAIL_FROM_NAME="Customer Portal"

# Plugin Development Mode
PLUGIN_DEBUG=true
PLUGIN_HOT_RELOAD=true
PLUGIN_SHOW_ERRORS=true
PLUGIN_CACHE_ENABLED=false
```

### 2.4 Database Setup

```bash
# Create database
mysql -u root -p -e "CREATE DATABASE customer_portal_dev;"

# Run migrations
php artisan migrate

# (Optional) Seed with test data
php artisan db:seed
```

### 2.5 Build Assets

```bash
# Development build (with source maps)
npm run dev

# Watch for changes (auto-rebuild)
npm run watch

# Production build (minified)
npm run build
```

### 2.6 Start Development Server

```bash
# Method 1: Laravel built-in server (recommended for development)
php artisan serve

# Your app is now at: http://localhost:8000

# Method 2: Custom port
php artisan serve --port=8080

# Method 3: Specific host (for network access)
php artisan serve --host=0.0.0.0 --port=8000
```

### 2.7 Enable Plugin System

The plugin system is already activated, but verify:

```bash
# 1. Ensure plugins are autoloaded
composer dump-autoload

# 2. Check plugin directory exists
ls -la plugins/

# 3. View available plugin commands
php artisan list | grep plugin

# Expected output:
#   make:plugin    Generate a new plugin
```

### 2.8 Development Workflow

```bash
# 1. Make code changes in plugins/ or app/

# 2. If you modified routes or config:
php artisan config:clear
php artisan route:clear

# 3. If you modified views:
php artisan view:clear

# 4. If you added new classes:
composer dump-autoload

# 5. Check logs for errors:
tail -f storage/logs/laravel.log

# 6. Test in browser:
# Open: http://localhost:8000
```

### 2.9 Local Testing URLs

| Page | URL | Purpose |
|------|-----|---------|
| **Home/Login** | http://localhost:8000 | Main portal login |
| **Settings** | http://localhost:8000/settings | Portal configuration |
| **Plugin Admin** | http://localhost:8000/admin/plugins | Plugin management |
| **Theme Customizer** | http://localhost:8000/theme/admin | Theme settings |

---

## 3. Dark Mode Plugin Proof of Concept

The portal already includes a theme-customizer plugin with dark mode support. Let's test it!

### 3.1 Verify Dark Mode Plugin

```bash
# Check if theme-customizer plugin exists
ls -la plugins/theme-customizer/

# View plugin configuration
cat plugins/theme-customizer/plugin.json

# You should see:
# "dark_mode_enabled": true
```

### 3.2 Enable Dark Mode

#### Method A: Via Configuration File

Edit `plugins/theme-customizer/plugin.json`:

```json
{
  "config": {
    "dark_mode_enabled": true,
    "dark_mode_default": true
  }
}
```

```bash
# Clear cache
php artisan cache:clear
php artisan config:clear

# Restart server
# Ctrl+C to stop, then: php artisan serve
```

#### Method B: Via Database (after migration)

```bash
# Run theme customizer migrations
php artisan migrate

# Insert user preference (if logged in as user ID 1):
mysql -u root -p customer_portal_dev -e "
INSERT INTO user_theme_preferences (user_id, theme, created_at, updated_at)
VALUES (1, 'dark', NOW(), NOW())
ON DUPLICATE KEY UPDATE theme='dark', updated_at=NOW();
"
```

#### Method C: Via Admin UI

```bash
# 1. Start server
php artisan serve

# 2. Navigate to theme admin
# http://localhost:8000/theme/admin

# 3. Toggle dark mode settings
# 4. Save changes
```

### 3.3 Test Dark Mode

```bash
# 1. Start development server
php artisan serve

# 2. Open browser: http://localhost:8000

# 3. Log in (or register test account)

# 4. Look for dark mode toggle in:
#    - User profile menu
#    - Settings page
#    - Navigation bar (if implemented)

# 5. Toggle dark mode and verify:
#    - Background changes to dark
#    - Text changes to light
#    - Colors adjust appropriately
```

### 3.4 View Dark Mode Implementation

```bash
# View service provider
cat plugins/theme-customizer/src/ThemeCustomizerServiceProvider.php

# View dark mode component
cat plugins/theme-customizer/resources/views/components/profile-dark-mode.blade.php

# View CSS (if exists)
find plugins/theme-customizer -name "*.css" -o -name "*.scss"
```

### 3.5 Create Custom Dark Mode Plugin (Alternative)

If you want to create your own from scratch:

```bash
# Generate new plugin
php artisan make:plugin "DarkModePlugin" \
  --author="Your Name" \
  --description="Custom dark mode implementation"

# Plugin created at: plugins/dark-mode-plugin/
```

Edit the plugin files:

**plugins/dark-mode-plugin/plugin.json:**
```json
{
    "name": "dark-mode-plugin",
    "version": "1.0.0",
    "description": "Simple dark mode toggle",
    "author": "Your Name",
    "enabled": true,
    "service_provider": "Plugins\\DarkModePlugin\\DarkModePluginServiceProvider",
    "config": {
        "default_theme": "light",
        "allow_user_toggle": true
    }
}
```

**plugins/dark-mode-plugin/resources/assets/css/dark-mode.css:**
```css
/* Dark mode styles */
body.dark-mode {
    background-color: #1a1a1a;
    color: #f0f0f0;
}

body.dark-mode .navbar {
    background-color: #2d2d2d !important;
}

body.dark-mode .card {
    background-color: #2d2d2d;
    border-color: #404040;
}

body.dark-mode .btn-primary {
    background-color: #4a9eff;
    border-color: #4a9eff;
}
```

**plugins/dark-mode-plugin/resources/assets/js/dark-mode.js:**
```javascript
// Dark mode toggle
document.addEventListener('DOMContentLoaded', function() {
    // Check saved preference
    const theme = localStorage.getItem('theme') || 'light';
    if (theme === 'dark') {
        document.body.classList.add('dark-mode');
    }

    // Add toggle button (if exists)
    const toggleBtn = document.getElementById('dark-mode-toggle');
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function() {
            document.body.classList.toggle('dark-mode');
            const newTheme = document.body.classList.contains('dark-mode') ? 'dark' : 'light';
            localStorage.setItem('theme', newTheme);
        });
    }
});
```

Update service provider to load assets:

**plugins/dark-mode-plugin/src/DarkModePluginServiceProvider.php:**
```php
<?php

namespace Plugins\DarkModePlugin;

use App\Providers\BasePluginServiceProvider;
use Illuminate\Support\Facades\View;

class DarkModePluginServiceProvider extends BasePluginServiceProvider
{
    protected string $pluginName = 'dark-mode-plugin';

    protected function bootPlugin(): void
    {
        parent::bootPlugin();

        // Publish assets
        $this->publishes([
            $this->pluginPath . '/resources/assets/css/dark-mode.css' 
                => public_path('plugins/dark-mode/dark-mode.css'),
            $this->pluginPath . '/resources/assets/js/dark-mode.js' 
                => public_path('plugins/dark-mode/dark-mode.js'),
        ], 'dark-mode-assets');

        // Add to all views
        View::composer('*', function ($view) {
            $view->with('darkModeEnabled', true);
        });
    }
}
```

```bash
# Publish assets
php artisan vendor:publish --tag=dark-mode-assets

# Clear cache
php artisan cache:clear

# Reload autoloader
composer dump-autoload
```

Add to your layout template (`resources/views/layouts/full.blade.php`):

```html
<head>
    <!-- ... existing head content ... -->
    <link rel="stylesheet" href="{{ asset('plugins/dark-mode/dark-mode.css') }}">
</head>
<body>
    <!-- ... existing body content ... -->
    
    <!-- Add dark mode toggle button -->
    <button id="dark-mode-toggle" class="btn btn-sm">
        🌙 Toggle Dark Mode
    </button>

    <script src="{{ asset('plugins/dark-mode/dark-mode.js') }}"></script>
</body>
```

---

## 4. Testing & Verification

### 4.1 Test Plugin System

```bash
# 1. Check installed plugins
php artisan tinker
```

```php
$manager = app(\App\Services\PluginManager::class);
$plugins = $manager->discoverPlugins();
dd($plugins->toArray());
```

Expected output:
```
array:3 [
  "theme-customizer" => [...],
  "markdowneditor" => [...],
  "example-plugin" => [...]
]
```

### 4.2 Test Dark Mode

**Manual Testing Checklist:**

- [ ] Dark mode toggle button appears
- [ ] Clicking toggle changes theme
- [ ] Theme persists after refresh
- [ ] All pages render correctly in dark mode
- [ ] Text is readable (sufficient contrast)
- [ ] Images/icons display properly
- [ ] Forms are usable
- [ ] Buttons have correct styling

### 4.3 Test Plugin Creation

```bash
# Create test plugin
php artisan make:plugin "TestPlugin" --author="Tester"

# Verify structure
ls -la plugins/test-plugin/

# Check files exist:
# - plugin.json
# - README.md
# - src/TestPluginServiceProvider.php
# - routes/web.php
# - resources/views/

# Reload autoloader
composer dump-autoload

# Check plugin loads
php artisan tinker
```

```php
$manager = app(\App\Services\PluginManager::class);
$plugins = $manager->discoverPlugins();
dd($plugins->has('test-plugin')); // Should be true
```

### 4.4 Test Hooks System

Create test in `plugins/test-plugin/src/TestPluginServiceProvider.php`:

```php
protected function registerHooks(): void
{
    app('plugin.hooks')->addAction('app.booted', function () {
        \Log::info('Test plugin: App booted!');
    });

    app('plugin.hooks')->addFilter('content.render', function ($content) {
        return $content . ' [Modified by Test Plugin]';
    });
}
```

Check logs:
```bash
tail -f storage/logs/laravel.log
```

### 4.5 Browser Testing

```bash
# Start server
php artisan serve

# Test pages:
# ✓ http://localhost:8000 (Home)
# ✓ http://localhost:8000/settings (Settings)
# ✓ http://localhost:8000/admin/plugins (Plugins - may need auth)
```

---

## 5. Troubleshooting

### 5.1 Common Issues - Local Development

#### Issue: Composer install fails

```bash
# Check PHP version
php -v  # Must be 8.2+

# Check PHP extensions
php -m | grep -E '(openssl|pdo|mbstring|tokenizer|xml)'

# Install missing extensions (Ubuntu):
sudo apt-get install php8.2-[extension-name]
```

#### Issue: Database connection refused

```bash
# Check MySQL is running
sudo systemctl status mysql  # Linux
# Or check MySQL in Services (Windows)

# Test connection
mysql -u root -p

# Fix if needed:
sudo systemctl start mysql  # Linux
```

#### Issue: Plugin not loading

```bash
# 1. Check plugin.json is valid
cat plugins/your-plugin/plugin.json | python -m json.tool

# 2. Check enabled status
# In plugin.json: "enabled": true

# 3. Reload autoloader
composer dump-autoload

# 4. Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 5. Check logs
tail -f storage/logs/laravel.log
```

#### Issue: Permission errors

```bash
# Fix storage permissions (Linux/Mac)
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Fix plugin permissions
chmod -R 755 plugins/
```

#### Issue: Assets not loading

```bash
# Rebuild assets
npm run dev

# Clear browser cache
# Ctrl+Shift+R (hard refresh)

# Check public directory
ls -la public/plugins/
```

### 5.2 Common Issues - Production (Docker)

#### Issue: Container won't start

```bash
# Check logs
sudo docker-compose logs app

# Rebuild container
sudo docker-compose down
sudo docker-compose up -d --build
```

#### Issue: Database connection error

```bash
# Check database container
sudo docker-compose ps

# Restart database
sudo docker-compose restart db

# Check credentials in .env
sudo docker-compose exec app cat .env | grep DB_
```

#### Issue: SSL certificate error

```bash
# Check certbot logs
sudo docker-compose logs letsencrypt

# Manually renew certificate
sudo docker-compose exec letsencrypt certbot renew
```

### 5.3 Debug Mode

Enable detailed error messages:

**.env:**
```env
APP_DEBUG=true
PLUGIN_DEBUG=true
PLUGIN_SHOW_ERRORS=true
```

**WARNING:** Never enable `APP_DEBUG=true` in production!

### 5.4 Log Locations

| Log Type | Location |
|----------|----------|
| Laravel | `storage/logs/laravel.log` |
| Docker | `sudo docker-compose logs` |
| Nginx | `sudo docker-compose logs nginx` |
| MySQL | `sudo docker-compose logs db` |

### 5.5 Reset Everything (Local Dev)

```bash
# ⚠️ WARNING: This deletes all data!

# 1. Drop database
mysql -u root -p -e "DROP DATABASE customer_portal_dev;"
mysql -u root -p -e "CREATE DATABASE customer_portal_dev;"

# 2. Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 3. Delete generated files
rm -rf storage/logs/*.log
rm -rf storage/framework/cache/*
rm -rf storage/framework/sessions/*
rm -rf storage/framework/views/*

# 4. Reinstall
composer install
php artisan migrate:fresh
php artisan key:generate

# 5. Rebuild assets
npm run dev
```

---

## 6. Next Steps

### For Development:
1. ✅ Setup complete - start creating plugins!
2. Read `PLUGIN_QUICK_START.md` for plugin development
3. Check `HOOKS_REFERENCE.md` for available hooks
4. Review `PLUGIN_SECURITY.md` for security best practices

### For Production:
1. ✅ Portal running - configure settings at `/settings`
2. Set up email (SMTP settings)
3. Configure payment gateways (PayPal/Stripe)
4. Test customer registration flow
5. Set up monitoring/backups

### Testing Dark Mode:
1. ✅ Plugin loaded - access theme customizer
2. Toggle dark mode in UI
3. Test all portal pages
4. Customize colors as needed
5. Deploy to production when ready

---

## Support Resources

- **Documentation**: All `PLUGIN_*.md` files in repository
- **Example Plugins**: `plugins/example-plugin/`, `plugins/theme-customizer/`
- **Logs**: `storage/logs/laravel.log`
- **Community**: GitHub Issues
- **Official Support**: support@sonar.software (for Sonar customers)

---

**Last Updated:** 2025-01-14  
**Guide Version:** 1.0  
**Tested On:** Ubuntu 22.04, Windows 11, macOS Ventura
