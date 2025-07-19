#!/bin/bash

echo "=== Customer Portal Local Setup ==="
echo ""

# Check if composer is installed
if ! command -v composer &> /dev/null; then
    echo "❌ Composer is not installed. Please install Composer first."
    echo "   Visit: https://getcomposer.org/download/"
    exit 1
fi

# Check if PHP is installed
if ! command -v php &> /dev/null; then
    echo "❌ PHP is not installed. Please install PHP 8.2+ first."
    exit 1
fi

# Check PHP version
PHP_VERSION=$(php -r "echo PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION;")
if [[ $(echo "$PHP_VERSION < 8.2" | bc -l) -eq 1 ]]; then
    echo "❌ PHP version $PHP_VERSION is too old. Please install PHP 8.2 or higher."
    exit 1
fi

echo "✅ PHP version $PHP_VERSION detected"

# Install dependencies
echo ""
echo "📦 Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader

if [ $? -ne 0 ]; then
    echo "❌ Failed to install dependencies"
    exit 1
fi

# Copy environment file
if [ ! -f .env ]; then
    echo ""
    echo "📝 Creating environment file..."
    cp .env.example .env
    echo "✅ Created .env file"
else
    echo "⚠️  .env file already exists"
fi

# Generate application key
echo ""
echo "🔑 Generating application key..."
php artisan key:generate

# Check if we can connect to database
echo ""
echo "🗄️  Setting up database..."
read -p "Do you have MySQL/MariaDB running locally? (y/n): " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    read -p "Database name (default: customer_portal): " DB_NAME
    DB_NAME=${DB_NAME:-customer_portal}
    
    read -p "Database username (default: root): " DB_USER
    DB_USER=${DB_USER:-root}
    
    read -s -p "Database password: " DB_PASS
    echo
    
    # Update .env file
    sed -i "s/DB_DATABASE=.*/DB_DATABASE=$DB_NAME/" .env
    sed -i "s/DB_USERNAME=.*/DB_USERNAME=$DB_USER/" .env
    sed -i "s/DB_PASSWORD=.*/DB_PASSWORD=$DB_PASS/" .env
    
    # Try to create database
    mysql -u "$DB_USER" -p"$DB_PASS" -e "CREATE DATABASE IF NOT EXISTS \`$DB_NAME\`;" 2>/dev/null
    
    if [ $? -eq 0 ]; then
        echo "✅ Database created/verified"
        
        # Run migrations
        echo "🔄 Running database migrations..."
        php artisan migrate --force
        
        if [ $? -eq 0 ]; then
            echo "✅ Database migrations completed"
        else
            echo "⚠️  Database migrations failed - check your database connection"
        fi
    else
        echo "⚠️  Could not connect to database - please check your credentials"
    fi
else
    echo "⚠️  Please set up MySQL/MariaDB and update the .env file manually"
fi

# Set up plugin system
echo ""
echo "🔌 Setting up plugin system..."

# Register PluginServiceProvider in app.php if not already registered
if ! grep -q "PluginServiceProvider" config/app.php; then
    # Add to providers array
    sed -i "/App\\\\Providers\\\\RouteServiceProvider::class,/a\\        App\\\\Providers\\\\PluginServiceProvider::class," config/app.php
    echo "✅ Registered PluginServiceProvider"
else
    echo "✅ PluginServiceProvider already registered"
fi

# Add plugin hook manager to AppServiceProvider
if ! grep -q "plugin.hooks" app/Providers/AppServiceProvider.php; then
    sed -i "/public function register()/a\\        \$this->app->singleton('plugin.hooks', function (\$app) {\n            return new \\\\App\\\\Services\\\\PluginHookManager();\n        });" app/Providers/AppServiceProvider.php
    echo "✅ Registered PluginHookManager"
else
    echo "✅ PluginHookManager already registered"
fi

# Publish plugin assets
echo ""
echo "📁 Publishing plugin assets..."
php artisan vendor:publish --tag=markdowneditor-assets --force

# Clear caches
echo ""
echo "🧹 Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear

echo ""
echo "🎉 Setup complete!"
echo ""
echo "To start the development server:"
echo "  php artisan serve"
echo ""
echo "Then visit:"
echo "  - Main app: http://localhost:8000"
echo "  - Markdown Editor: http://localhost:8000/markdowneditor"
echo "  - Test route: http://localhost:8000/test-route"
echo ""
echo "To test the markdown editor:"
echo "  1. Add class 'markdown-editor' to any textarea"
echo "  2. Include the plugin CSS and JS files"
echo "  3. The textarea will be automatically enhanced"
echo ""
echo "Plugin files are located in:"
echo "  - plugins/markdowneditor/"
echo ""
echo "Happy coding! 🚀"
