#!/bin/bash

# Customer Portal - Local Development Setup Script
# This script sets up the portal for local development with plugin system

set -e  # Exit on error

echo "╔════════════════════════════════════════════════════════════╗"
echo "║   Customer Portal - Local Development Setup                ║"
echo "║   With Plugin System Support                               ║"
echo "╚════════════════════════════════════════════════════════════╝"
echo ""

# Color codes
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Functions
print_success() {
    echo -e "${GREEN}✓${NC} $1"
}

print_error() {
    echo -e "${RED}✗${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}⚠${NC} $1"
}

print_info() {
    echo -e "${BLUE}ℹ${NC} $1"
}

print_step() {
    echo -e "\n${BLUE}▸${NC} $1"
}

# Check prerequisites
print_step "Checking prerequisites..."

# Check PHP
if ! command -v php &> /dev/null; then
    print_error "PHP is not installed"
    echo "Please install PHP 8.2+ from:"
    echo "  - Ubuntu/Debian: sudo apt-get install php8.2"
    echo "  - Mac: brew install php@8.2"
    echo "  - Windows: Download from php.net or use Laragon/XAMPP"
    exit 1
fi

PHP_VERSION=$(php -r "echo PHP_VERSION;")
PHP_MAJOR=$(php -r "echo PHP_MAJOR_VERSION;")
PHP_MINOR=$(php -r "echo PHP_MINOR_VERSION;")

if [ "$PHP_MAJOR" -lt 8 ] || ([ "$PHP_MAJOR" -eq 8 ] && [ "$PHP_MINOR" -lt 2 ]); then
    print_error "PHP version $PHP_VERSION is too old"
    echo "Please install PHP 8.2 or higher"
    exit 1
fi
print_success "PHP $PHP_VERSION detected"

# Check Composer
if ! command -v composer &> /dev/null; then
    print_error "Composer is not installed"
    echo "Please install from: https://getcomposer.org/download/"
    exit 1
fi
COMPOSER_VERSION=$(composer --version | grep -oP '\d+\.\d+\.\d+' | head -1)
print_success "Composer $COMPOSER_VERSION detected"

# Check MySQL/MariaDB
if command -v mysql &> /dev/null; then
    MYSQL_VERSION=$(mysql --version | grep -oP '\d+\.\d+\.\d+' | head -1)
    print_success "MySQL/MariaDB $MYSQL_VERSION detected"
else
    print_warning "MySQL not found in PATH"
    echo "Make sure MySQL/MariaDB is installed and running"
    echo "  - Ubuntu/Debian: sudo apt-get install mysql-server"
    echo "  - Mac: brew install mysql"
    echo "  - Windows: Included in Laragon/XAMPP/WAMP"
fi

# Check Node.js (optional but recommended)
if command -v node &> /dev/null; then
    NODE_VERSION=$(node --version)
    print_success "Node.js $NODE_VERSION detected"
else
    print_warning "Node.js not found (optional)"
    echo "Install from: https://nodejs.org/"
fi

# Install PHP dependencies
print_step "Installing PHP dependencies..."
if [ -f "composer.lock" ]; then
    print_info "composer.lock found, using existing versions"
    composer install --no-interaction
else
    print_info "Installing fresh dependencies"
    composer install --no-interaction
fi

if [ $? -eq 0 ]; then
    print_success "PHP dependencies installed"
else
    print_error "Failed to install PHP dependencies"
    exit 1
fi

# Create .env file
print_step "Setting up environment configuration..."
if [ ! -f ".env" ]; then
    cp .env.example .env
    print_success "Created .env file"
    
    # Generate app key
    php artisan key:generate --ansi
    print_success "Generated application key"
else
    print_warning ".env file already exists, skipping"
fi

# Update .env for local development
print_step "Configuring for local development..."
sed -i.bak 's/APP_ENV=production/APP_ENV=local/' .env 2>/dev/null || \
    sed -i '' 's/APP_ENV=production/APP_ENV=local/' .env 2>/dev/null
sed -i.bak 's/APP_DEBUG=false/APP_DEBUG=true/' .env 2>/dev/null || \
    sed -i '' 's/APP_DEBUG=false/APP_DEBUG=true/' .env 2>/dev/null

# Add plugin development settings if not present
if ! grep -q "PLUGIN_DEBUG" .env; then
    echo "" >> .env
    echo "# Plugin Development Settings" >> .env
    echo "PLUGIN_DEBUG=true" >> .env
    echo "PLUGIN_HOT_RELOAD=true" >> .env
    echo "PLUGIN_SHOW_ERRORS=true" >> .env
    echo "PLUGIN_CACHE_ENABLED=false" >> .env
    print_success "Added plugin development settings to .env"
fi

# Database setup
print_step "Database configuration..."
print_info "Please ensure MySQL/MariaDB is running"

# Prompt for database credentials
read -p "Database name [customer_portal_dev]: " DB_NAME
DB_NAME=${DB_NAME:-customer_portal_dev}

read -p "Database user [root]: " DB_USER
DB_USER=${DB_USER:-root}

read -sp "Database password: " DB_PASS
echo ""

# Update .env with database credentials
sed -i.bak "s/DB_DATABASE=.*/DB_DATABASE=$DB_NAME/" .env 2>/dev/null || \
    sed -i '' "s/DB_DATABASE=.*/DB_DATABASE=$DB_NAME/" .env 2>/dev/null
sed -i.bak "s/DB_USERNAME=.*/DB_USERNAME=$DB_USER/" .env 2>/dev/null || \
    sed -i '' "s/DB_USERNAME=.*/DB_USERNAME=$DB_USER/" .env 2>/dev/null
sed -i.bak "s/DB_PASSWORD=.*/DB_PASSWORD=$DB_PASS/" .env 2>/dev/null || \
    sed -i '' "s/DB_PASSWORD=.*/DB_PASSWORD=$DB_PASS/" .env 2>/dev/null

# Try to create database
print_info "Creating database..."
mysql -u "$DB_USER" -p"$DB_PASS" -e "CREATE DATABASE IF NOT EXISTS $DB_NAME;" 2>/dev/null

if [ $? -eq 0 ]; then
    print_success "Database '$DB_NAME' ready"
else
    print_warning "Could not create database automatically"
    echo "Please create it manually: CREATE DATABASE $DB_NAME;"
fi

# Run migrations
print_step "Running database migrations..."
php artisan migrate --force

if [ $? -eq 0 ]; then
    print_success "Database migrations completed"
else
    print_error "Migration failed"
    echo "Check database credentials and try: php artisan migrate"
fi

# Install Node dependencies (if package.json exists)
if [ -f "package.json" ]; then
    print_step "Installing Node.js dependencies..."
    if command -v npm &> /dev/null; then
        npm install
        print_success "Node.js dependencies installed"
        
        print_step "Building frontend assets..."
        npm run dev
        print_success "Frontend assets built"
    else
        print_warning "npm not found, skipping Node.js dependencies"
    fi
fi

# Ensure plugins directory exists
if [ ! -d "plugins" ]; then
    mkdir -p plugins
    print_success "Created plugins directory"
fi

# Set permissions (Linux/Mac)
if [ "$(uname)" != "MINGW"* ] && [ "$(uname)" != "MSYS"* ]; then
    print_step "Setting permissions..."
    chmod -R 775 storage bootstrap/cache 2>/dev/null
    chmod -R 755 plugins 2>/dev/null
    print_success "Permissions set"
fi

# Dump autoload to include plugins
print_step "Updating autoloader..."
composer dump-autoload
print_success "Autoloader updated"

# Clear all caches
print_step "Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
print_success "Caches cleared"

# Summary
echo ""
echo "╔════════════════════════════════════════════════════════════╗"
echo "║   Setup Complete! 🎉                                       ║"
echo "╚════════════════════════════════════════════════════════════╝"
echo ""
echo -e "${GREEN}Your development environment is ready!${NC}"
echo ""
echo "📋 Next steps:"
echo ""
echo "1. Start the development server:"
echo -e "   ${BLUE}php artisan serve${NC}"
echo ""
echo "2. Access the portal:"
echo -e "   ${BLUE}http://localhost:8000${NC}"
echo ""
echo "3. Configure settings:"
echo -e "   ${BLUE}http://localhost:8000/settings${NC}"
echo ""
echo "4. Create your first plugin:"
echo -e "   ${BLUE}php artisan make:plugin \"MyPlugin\" --author=\"Your Name\"${NC}"
echo ""
echo "5. View plugin management:"
echo -e "   ${BLUE}http://localhost:8000/admin/plugins${NC}"
echo ""
echo "📚 Documentation:"
echo "   - SETUP_GUIDE_COMPLETE.md - Complete setup guide"
echo "   - PLUGIN_QUICK_START.md - Plugin development"
echo "   - PLUGIN_ARCHITECTURE_PLAN.md - Full architecture"
echo "   - UI_DESIGN_DISCUSSION.md - Design patterns"
echo ""
echo "🐛 Troubleshooting:"
echo "   - Logs: storage/logs/laravel.log"
echo "   - Clear cache: php artisan cache:clear"
echo "   - Database: php artisan migrate"
echo ""
echo -e "${YELLOW}Note:${NC} For production deployment, see SETUP_GUIDE_COMPLETE.md"
echo ""
