#!/bin/bash

# Ubuntu Quick Setup Script for Customer Portal with Markdown Editor Plugin
echo "🐧 Setting up Customer Portal on Ubuntu..."

# Check if running as root
if [ "$EUID" -eq 0 ]; then
    echo "❌ Please don't run this script as root (no sudo)"
    exit 1
fi

# Update system
echo "📦 Updating system packages..."
sudo apt update

# Install PHP 8.1 and required extensions
echo "🐘 Installing PHP 8.1 and extensions..."
sudo apt install software-properties-common -y
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

sudo apt install php8.1 php8.1-cli php8.1-common php8.1-mysql php8.1-xml php8.1-xmlrpc php8.1-curl php8.1-gd php8.1-imagick php8.1-cli php8.1-dev php8.1-imap php8.1-mbstring php8.1-opcache php8.1-soap php8.1-zip php8.1-intl php8.1-bcmath php8.1-fpm php8.1-fileinfo -y

# Install Composer
echo "🎼 Installing Composer..."
if ! command -v composer &> /dev/null; then
    curl -sS https://getcomposer.org/installer | php
    sudo mv composer.phar /usr/local/bin/composer
    sudo chmod +x /usr/local/bin/composer
else
    echo "✅ Composer already installed"
fi

# Install project dependencies
echo "📚 Installing Laravel dependencies..."
composer install --ignore-platform-reqs

# Copy environment file
echo "⚙️ Setting up environment..."
if [ ! -f .env ]; then
    cp .env.example .env
fi

# Generate application key
echo "🔑 Generating application key..."
php artisan key:generate --force

# Set proper permissions
echo "🔐 Setting file permissions..."
sudo chown -R $USER:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

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
fi

echo ""
echo "🎉 Setup Complete!"
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
echo "   Then visit: http://YOUR_UBUNTU_IP:8000/markdowneditor"
echo ""
