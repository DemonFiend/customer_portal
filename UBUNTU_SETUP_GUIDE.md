# 🐧 Ubuntu Setup Guide - Customer Portal with Markdown Editor

## 🚀 Quick Fork & Setup Process

### Step 1: Fork the Repository
1. **Go to**: https://github.com/DemonFiend/customer_portal
2. **Click "Fork"** in the top-right corner
3. **Create fork** to your GitHub account

### Step 2: Clone on Ubuntu Machine
```bash
# Clone your fork
git clone https://github.com/YOUR_USERNAME/customer_portal.git
cd customer_portal

# Or if you want to use the original repo directly:
# git clone https://github.com/DemonFiend/customer_portal.git
```

### Step 3: Ubuntu Dependencies Installation
```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install PHP 8.1 (recommended for Laravel 10)
sudo apt install software-properties-common -y
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

# Install PHP 8.1 and required extensions
sudo apt install php8.1 php8.1-cli php8.1-common php8.1-mysql php8.1-xml php8.1-xmlrpc php8.1-curl php8.1-gd php8.1-imagick php8.1-cli php8.1-dev php8.1-imap php8.1-mbstring php8.1-opcache php8.1-soap php8.1-zip php8.1-intl php8.1-bcmath php8.1-fpm php8.1-fileinfo -y

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer

# Install MySQL (optional - only needed for full portal functionality)
sudo apt install mysql-server -y

# Install Git (if not already installed)
sudo apt install git -y
```

### Step 4: Laravel Setup
```bash
# Install dependencies
composer install --ignore-platform-reqs

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Set proper permissions
sudo chown -R $USER:www-data storage
sudo chown -R $USER:www-data bootstrap/cache
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

### Step 5: Configure Environment
Edit `.env` file:
```bash
nano .env
```

Add these Laravel essentials:
```env
APP_NAME="Customer Portal"
APP_ENV=local
APP_KEY=base64:YOUR_GENERATED_KEY_HERE
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database (optional for markdown editor testing)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=customer_portal
DB_USERNAME=root
DB_PASSWORD=

# Session and Cache
SESSION_DRIVER=file
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
```

### Step 6: Start the Server
```bash
# Start Laravel development server
php artisan serve

# Or bind to all interfaces to access from other machines
php artisan serve --host=0.0.0.0 --port=8000
```

### Step 7: Test Markdown Editor
Open browser and visit:
- **Main Editor**: http://localhost:8000/markdowneditor
- **Integration Demo**: http://localhost:8000/markdowneditor/demo
- **Test Route**: http://localhost:8000/test-route

## 🎯 Alternative: Docker Setup (Even Easier)

If you prefer Docker (which this repo supports):

```bash
# Clone the repo
git clone https://github.com/YOUR_USERNAME/customer_portal.git
cd customer_portal

# Install Docker and Docker Compose
sudo apt install docker.io docker-compose -y
sudo usermod -aG docker $USER
# Log out and back in for group changes

# Start with Docker
docker-compose up -d

# Access at http://localhost
```

## 📤 Publishing Your Fork

### Option A: Keep it Simple
1. **Work directly** in your forked repo
2. **Test the markdown editor** 
3. **Make any customizations** you need
4. **Keep private** or make public as needed

### Option B: Clean Release
1. **Create a new branch** for your plugin work:
   ```bash
   git checkout -b markdown-editor-plugin
   git add .
   git commit -m "Add functional markdown editor plugin system"
   git push origin markdown-editor-plugin
   ```

2. **Create a release** on GitHub with just the plugin files
3. **Share the specific files** people need

## 🔧 What's Already Ready

The markdown editor plugin I built includes:

✅ **Complete Plugin System**
- `app/Services/PluginManager.php`
- `app/Services/PluginHookManager.php` 
- `app/Providers/BasePluginServiceProvider.php`

✅ **Functional Markdown Editor**
- `plugins/markdowneditor/` - Complete plugin
- Live preview, toolbar, multiple integration methods
- Responsive design, dark theme support

✅ **Integration Ready**
- Service provider auto-registration
- Asset publishing system
- Multiple initialization methods

## 🚀 Expected Results on Ubuntu

On Ubuntu, you should see:
- ✅ **Faster installation** (native environment)
- ✅ **No PHP compatibility issues**
- ✅ **Proper file permissions** 
- ✅ **Better performance**
- ✅ **Docker support** (if needed)

The markdown editor will work immediately at:
- http://localhost:8000/markdowneditor

## 🐛 Ubuntu Troubleshooting

If you encounter issues:

```bash
# Check PHP version
php -v

# Check required extensions
php -m | grep -E "(fileinfo|mbstring|xml|curl)"

# Fix permissions
sudo chown -R $USER:$USER .
chmod -R 755 .

# Clear Laravel cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

## 💡 Why Ubuntu Will Work Better

1. **Native Environment** - Laravel designed for Linux
2. **Proper Permissions** - No Windows file system issues  
3. **Package Managers** - apt handles dependencies cleanly
4. **Docker Support** - Full containerization available
5. **Performance** - Much faster than Windows for PHP

Your markdown editor plugin is ready to work perfectly on Ubuntu! 🎉
