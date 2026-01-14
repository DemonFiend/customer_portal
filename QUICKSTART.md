# Quick Start - Local Development

Get the Customer Portal with plugin system running on your local machine in minutes!

## 🚀 Quick Setup (One Command)

### Linux / macOS:
```bash
./local-dev-setup.sh
```

### Windows (Git Bash / WSL):
```bash
bash local-dev-setup.sh
```

### Windows (PowerShell):
```powershell
php setup-local.bat
```

The script will:
- ✅ Check prerequisites (PHP 8.2+, Composer, MySQL)
- ✅ Install dependencies
- ✅ Set up environment configuration
- ✅ Create database
- ✅ Run migrations
- ✅ Build frontend assets
- ✅ Configure plugin system

---

## 📋 Manual Setup (Step by Step)

If you prefer manual setup or the script fails:

```bash
# 1. Install dependencies
composer install
npm install

# 2. Configure environment
cp .env.example .env
php artisan key:generate

# 3. Edit .env file with your database credentials
# DB_DATABASE=customer_portal_dev
# DB_USERNAME=root
# DB_PASSWORD=your_password

# 4. Create database
mysql -u root -p -e "CREATE DATABASE customer_portal_dev;"

# 5. Run migrations
php artisan migrate

# 6. Build assets
npm run dev

# 7. Start server
php artisan serve
```

---

## 🎨 Test Dark Mode Plugin

The portal includes a theme-customizer plugin with dark mode support!

### Enable Dark Mode:

1. Start the server: `php artisan serve`
2. Navigate to: http://localhost:8000
3. Log in or create account
4. Find the dark mode toggle (usually in profile menu or settings)
5. Click to toggle between light/dark themes

### Via Configuration:

Edit `plugins/theme-customizer/plugin.json`:
```json
{
  "config": {
    "dark_mode_enabled": true,
    "dark_mode_default": true
  }
}
```

Then restart server:
```bash
php artisan cache:clear
php artisan serve
```

---

## 🔧 Create Your First Plugin

```bash
# Generate plugin
php artisan make:plugin "MyAwesomePlugin" --author="Your Name"

# Your plugin is created at:
# plugins/my-awesome-plugin/

# Reload autoloader
composer dump-autoload

# Start developing!
```

---

## 📚 Learn More

| Document | What's Inside |
|----------|---------------|
| **SETUP_GUIDE_COMPLETE.md** | Full setup guide (local + production) |
| **PLUGIN_QUICK_START.md** | Create plugins in 5 minutes |
| **PLUGIN_ARCHITECTURE_PLAN.md** | Complete system architecture |
| **UI_DESIGN_DISCUSSION.md** | Design patterns & feedback |
| **HOOKS_REFERENCE.md** | All available hooks & filters |

---

## 🐛 Troubleshooting

### Portal won't start?
```bash
# Check logs
tail -f storage/logs/laravel.log

# Clear everything
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Plugin not loading?
```bash
# Reload autoloader
composer dump-autoload

# Check plugin.json
cat plugins/your-plugin/plugin.json

# Ensure enabled: true
```

### Database errors?
```bash
# Check MySQL is running
# Linux: sudo systemctl status mysql
# Mac: brew services list
# Windows: Check Services app

# Test connection
mysql -u root -p

# Recreate database
mysql -u root -p -e "DROP DATABASE customer_portal_dev;"
mysql -u root -p -e "CREATE DATABASE customer_portal_dev;"
php artisan migrate
```

---

## ⚡ Development URLs

| Page | URL |
|------|-----|
| **Home** | http://localhost:8000 |
| **Settings** | http://localhost:8000/settings |
| **Plugin Admin** | http://localhost:8000/admin/plugins |
| **Theme Admin** | http://localhost:8000/theme/admin |

---

## 🎯 Next Steps

1. ✅ **Portal running?** Start creating plugins!
2. 📖 **Read docs** - Check PLUGIN_QUICK_START.md
3. 🎨 **Customize theme** - Edit theme-customizer plugin
4. 🔧 **Add features** - Use hooks & filters
5. 🚀 **Deploy** - See SETUP_GUIDE_COMPLETE.md

---

## 💬 Need Help?

- **Logs**: `storage/logs/laravel.log`
- **Docs**: All PLUGIN_*.md files
- **Examples**: `plugins/example-plugin/`, `plugins/theme-customizer/`
- **Issues**: GitHub Issues

---

**Happy Coding!** 🎉
