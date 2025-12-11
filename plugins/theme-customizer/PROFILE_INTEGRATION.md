# Adding Dark Mode Toggle to Profile Page

## Instructions

To add the dark mode toggle to the user profile page, follow these steps:

### Option 1: Quick Integration (Recommended)

Add this line to `/resources/views/pages/profile/show.blade.php` right after the opening container div:

```blade
@include('theme-customizer::components.profile-dark-mode')
```

**Example placement:**
```blade
@extends('layouts.full')
@section('content')
<div class="container-fluid">
   @include('theme-customizer::components.profile-dark-mode')
   
   <div class="row justify-content-center">
      <!-- Rest of profile content -->
```

### Option 2: Custom Placement

You can place the dark mode toggle anywhere in the profile page by using the include statement:

```blade
<!-- Wherever you want the dark mode toggle -->
@include('theme-customizer::components.profile-dark-mode')
```

### What It Does

The dark mode component:
- ✅ Shows a card with theme preference toggle
- ✅ Automatically loads user's saved preference
- ✅ Saves preference to database via API
- ✅ Updates theme in real-time
- ✅ Works with admin page theme settings
- ✅ Responsive and accessible

### Component Features

- **Toggle Switch**: Smooth animated switch
- **Status Display**: Shows current theme (Light/Dark Mode)
- **API Integration**: Uses `/portal/theme/toggle` and `/portal/theme/preference`
- **Persistence**: Saves to `user_theme_preferences` table
- **Styling**: Matches portal design with card layout

### API Endpoints

The component uses these authenticated endpoints:

- `GET /portal/theme/preference` - Get user's current theme
- `POST /portal/theme/toggle` - Toggle dark mode on/off

### Database

Theme preferences are stored in:
- Table: `user_theme_preferences`
- Fields: `contact_id`, `dark_mode`, `created_at`, `updated_at`

### Testing

1. Navigate to `/portal/profile` (must be logged in)
2. Look for "Theme Preference" card
3. Toggle the switch
4. Refresh page - preference should persist
5. Check admin page (`/admin`) - global setting should enable/disable feature

### Styling

The component includes self-contained CSS for:
- Toggle switch design
- Dark mode color variables
- Smooth transitions
- Responsive layout

### Troubleshooting

**Toggle not working?**
- Ensure migration has been run: `php artisan migrate`
- Check that plugin routes are loaded
- Verify user is authenticated
- Check browser console for API errors

**Preference not saving?**
- Check database table exists
- Verify API endpoints are accessible
- Check CSRF token is present in page

**Dark mode not applying?**
- Ensure dark mode is enabled in admin settings
- Check that CSS variables are defined in main layout
- Verify JavaScript is loading correctly

### Manual Installation

If you need to manually integrate:

1. **Run Migration**
```bash
php artisan migrate
```

2. **Add Include to Profile View**
```blade
@include('theme-customizer::components.profile-dark-mode')
```

3. **Verify Routes**
The plugin automatically registers these routes via `routes/web.php`

4. **Test**
Visit `/portal/profile` and toggle dark mode

### Customization

To customize the appearance, edit:
```
plugins/theme-customizer/resources/views/components/profile-dark-mode.blade.php
```

You can modify:
- Card styling
- Toggle switch design
- Status messages
- Icons
- Layout

### Admin Control

Admins can control dark mode feature via `/admin`:
- Enable/disable dark mode feature globally
- Set default theme (light/dark)
- Restart server after changes

---

**Note**: The dark mode component requires the theme-customizer plugin to be enabled and the database migration to be run.
