# SCXP-BetterPortal Core Functions - Implementation Summary

## Overview

SCXP-BetterPortal core functions have been implemented to enhance the admin capabilities of the customer portal with two powerful features accessible from `/admin`.

## Features Implemented

### 1. Plugin Creator UI 🎨

**Location:** `/admin/plugin-creator`

**Features:**
- **User-Friendly Form** with the following inputs:
  - **Plugin Name** (required) - Converted to PascalCase automatically
  - **Author Name** (required) - Your name or organization
  - **Description** (optional) - Brief description of plugin functionality
  - **Restart After Installing** (checkbox) - Auto-restart server after creation

**What It Does:**
1. Executes `php artisan make:plugin` command with provided parameters
2. Creates complete plugin directory structure:
   - Service provider with auto-loading
   - Controllers, models, routes scaffolding
   - View templates
   - Configuration files
   - Database migration template
   - README documentation
3. Automatically runs `composer dump-autoload` to register new plugin
4. Optionally restarts server if checkbox is selected
5. Provides success/error feedback

**Access:** Click "Create Plugin" button on `/admin` page

### 2. Server Restart Function 🔄

**Location:** Button on `/admin` page

**Features:**
- **Confirmation Dialog** - Prevents accidental restarts
- **Docker Container Restart** - Executes `docker-compose restart`
- **User Notification** - Shows alert with instructions:
  - "Server restart initiated!"
  - "Please refresh this page in 1-2 minutes"
  - Displays estimated time with countdown icon

**What It Does:**
1. Executes Docker restart command
2. Attempts `docker-compose restart app` first
3. Falls back to `docker-compose restart` if needed
4. Shows restart notification with timing guidance
5. Ensures users know to wait before refreshing

**Use Cases:**
- Apply plugin updates
- Reload configuration changes
- Reset server state after installations
- Troubleshooting

## Admin Page Layout

The admin page now includes a dedicated "SCXP-BetterPortal Core Functions" card with:

- **Purple gradient header** - Distinctive branding
- **Two-column layout:**
  - Left: Plugin Creator button
  - Right: Server Restart button
- **Dark mode compatible** - Respects theme preference
- **Responsive design** - Works on all screen sizes

## Security

All SCXP-BetterPortal functions require:
- Same authentication key as `/settings` page
- Session-based authentication (`settings_authenticated`)
- Input validation on all form submissions
- Confirmation dialogs for destructive actions
- Error handling and user feedback

## Integration Points

### Routes Added
```php
// Plugin Creator
GET  /admin/plugin-creator
POST /admin/plugin-creator

// Server Restart
POST /admin/restart-server
```

### Controller Methods
```php
ThemeAdminController::showPluginCreator()    // Show form
ThemeAdminController::createPlugin()         // Process creation
ThemeAdminController::restartServer()        // Restart server
```

### Views Created
- `admin/plugin-creator.blade.php` - Plugin creation form
- `admin/index.blade.php` - Updated with core functions card

## Usage Example

### Creating a Plugin

1. Navigate to `/admin` (authenticate with settings key)
2. Click "Create Plugin" in SCXP-BetterPortal section
3. Fill in the form:
   ```
   Plugin Name: Custom Analytics
   Author: Your Company
   Description: Track custom metrics and analytics
   ☑ Restart After Installing
   ```
4. Click "Create Plugin"
5. Plugin is created at `plugins/custom-analytics/`
6. If restart checked: Server restarts, wait 1-2 minutes
7. Plugin is automatically discovered and loaded

### Restarting Server

1. Navigate to `/admin`
2. Click "Restart Server" button
3. Confirm the action in dialog
4. Wait for restart notification
5. Refresh page after 1-2 minutes
6. Server is restarted with latest changes applied

## Benefits

### Plugin Creator
- ✅ No command line access needed
- ✅ Quick plugin scaffolding
- ✅ Reduces errors from manual setup
- ✅ Consistent plugin structure
- ✅ Immediate availability after creation

### Server Restart
- ✅ Apply changes without SSH
- ✅ Safe restart with user notification
- ✅ Docker-aware restart process
- ✅ Prevents confusion about timing
- ✅ Emergency troubleshooting tool

## Technical Details

### Plugin Creation Process
```
1. Validate input (name, author, description)
2. Generate PascalCase class name from plugin name
3. Execute: php artisan make:plugin "Name" --author="Author" --description="Desc"
4. Execute: composer dump-autoload
5. If restart_after=true: Trigger server restart
6. Return success/error message
```

### Server Restart Process
```
1. Check authentication
2. Execute: docker-compose restart app
3. If error, fallback: docker-compose restart
4. Set session flash message with restart notification
5. Redirect to /admin with instructions
```

## Future Enhancements

Potential additions to SCXP-BetterPortal:

- **Plugin Manager UI** - Enable/disable/delete plugins
- **Plugin Marketplace** - Browse and install community plugins
- **Configuration Editor** - Edit plugin.json through UI
- **Log Viewer** - View server and plugin logs
- **Health Check** - Monitor server and plugin status
- **Backup/Restore** - Plugin backup and restoration
- **Update Checker** - Check for plugin updates

## Notes

- Plugin creator uses Laravel's artisan command system
- Server restart requires Docker/docker-compose to be available
- All operations are logged for auditing
- Error handling provides clear feedback to users
- Functions are designed to be non-destructive
- SCXP-BetterPortal branding distinguishes core functions from regular plugin features

---

**Version:** 1.0.0  
**Last Updated:** 2025-12-11  
**Part of:** theme-customizer plugin
