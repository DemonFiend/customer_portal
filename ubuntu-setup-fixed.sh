#!/bin/bash

# Ubuntu Quick Setup Script for Customer Portal with Markdown Editor Plugin - FIXED VERSION
echo "🐧 Setting up Customer Portal on Ubuntu..."

# Check if running as root
if [ "$EUID" -eq 0 ]; then
    echo "❌ Please don't run this script as root (no sudo)"
    exit 1
fi

# Update system
echo "📦 Updating system packages..."
sudo apt update

# Install curl first (needed for Composer)
echo "🌐 Installing curl..."
sudo apt install curl -y

# Install PHP 8.1 and required extensions
echo "🐘 Installing PHP 8.1 and extensions..."
sudo apt install software-properties-common -y
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

sudo apt install php8.1 php8.1-cli php8.1-common php8.1-mysql php8.1-xml php8.1-xmlrpc php8.1-curl php8.1-gd php8.1-imagick php8.1-cli php8.1-dev php8.1-imap php8.1-mbstring php8.1-opcache php8.1-soap php8.1-zip php8.1-intl php8.1-bcmath php8.1-fpm php8.1-fileinfo -y

# Install Composer
echo "🎼 Installing Composer..."
if ! command -v composer &> /dev/null; then
    echo "📥 Downloading Composer installer..."
    curl -sS https://getcomposer.org/installer -o composer-setup.php
    
    echo "🔍 Verifying installer signature..."
    HASH="$(wget -q -O - https://composer.github.io/installer.sig)"
    php -r "if (hash_file('SHA384', 'composer-setup.php') === '$HASH') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
    
    echo "🚀 Installing Composer..."
    php composer-setup.php --install-dir=/tmp --filename=composer
    sudo mv /tmp/composer /usr/local/bin/composer
    sudo chmod +x /usr/local/bin/composer
    rm composer-setup.php
    
    echo "✅ Composer installed successfully!"
    composer --version
else
    echo "✅ Composer already installed"
fi

# Verify Composer is working
if ! command -v composer &> /dev/null; then
    echo "❌ Composer installation failed. Let's try alternative method..."
    
    # Alternative: install via apt
    sudo apt install composer -y
    
    if ! command -v composer &> /dev/null; then
        echo "❌ Could not install Composer. Please install manually:"
        echo "   wget https://getcomposer.org/composer-stable.phar"
        echo "   sudo mv composer-stable.phar /usr/local/bin/composer"
        echo "   sudo chmod +x /usr/local/bin/composer"
        exit 1
    fi
fi

# Install project dependencies
echo "📚 Installing Laravel dependencies..."
composer install --ignore-platform-reqs --no-interaction

# Check if vendor directory was created
if [ ! -d "vendor" ]; then
    echo "❌ Composer install failed. Trying without platform requirements..."
    composer install --no-dev --ignore-platform-reqs --no-interaction
fi

if [ ! -d "vendor" ]; then
    echo "❌ Still no vendor directory. Let's try updating first..."
    composer update --ignore-platform-reqs --no-interaction
fi

# Copy environment file
echo "⚙️ Setting up environment..."
if [ ! -f .env ]; then
    cp .env.example .env
    echo "✅ Environment file created"
else
    echo "✅ Environment file already exists"
fi

# Add Laravel configuration to .env
echo "📝 Configuring Laravel environment..."
if ! grep -q "APP_NAME" .env; then
    cat >> .env << EOL

# Laravel Configuration
APP_NAME="Customer Portal"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Session and Cache
SESSION_DRIVER=file
CACHE_DRIVER=file
QUEUE_CONNECTION=sync

# Database (optional for markdown editor testing)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=customer_portal
DB_USERNAME=root
DB_PASSWORD=
EOL
    echo "✅ Laravel configuration added to .env"
fi

# Generate application key
echo "🔑 Generating application key..."
if [ -d "vendor" ]; then
    php artisan key:generate --force
    echo "✅ Application key generated"
else
    echo "⚠️ Cannot generate key without vendor directory"
    echo "🔧 You may need to run: composer install"
fi

# Set proper permissions
echo "🔐 Setting file permissions..."
if [ -d "storage" ]; then
    sudo chown -R $USER:www-data storage bootstrap/cache 2>/dev/null || sudo chown -R $USER:$USER storage bootstrap/cache
    chmod -R 775 storage bootstrap/cache
    echo "✅ Permissions set"
fi

echo ""
echo "🎉 Setup Complete!"
echo ""

# Check if everything is ready
if [ -d "vendor" ] && [ -f ".env" ]; then
    echo "✅ All dependencies installed successfully!"
    echo ""
    echo "🚀 To start the server:"
    echo "   php artisan serve"
    echo ""
    echo "🧪 Test the Markdown Editor at:"
    echo "   http://localhost:8000/markdowneditor"
    echo "   http://localhost:8000/markdowneditor/demo"
    echo "   http://localhost:8000/test-route"
    echo ""
    echo "📖 For external access:"
    echo "   php artisan serve --host=0.0.0.0 --port=8000"
    echo "   Then visit: http://$(hostname -I | awk '{print $1}'):8000/markdowneditor"
else
    echo "⚠️ Setup completed with some issues. You may need to:"
    echo "   1. Run: composer install"
    echo "   2. Run: php artisan key:generate"
    echo "   3. Then: php artisan serve"
fi

echo ""
