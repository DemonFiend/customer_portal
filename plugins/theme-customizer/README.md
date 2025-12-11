# Theme Customizer Plugin

A simple yet powerful plugin that demonstrates how to customize the visual appearance of the customer portal.

## Features

- **Dynamic Color Scheme**: Easily change primary and secondary colors
- **Custom Logo Support**: Add your company logo
- **Custom CSS Injection**: Automatically applies custom styles
- **Footer Customization**: Change footer text
- **Blade Directives**: New directives for theme access in views

## Configuration

Edit `plugin.json` to customize the theme:

```json
{
    "config": {
        "primary_color": "#007bff",      // Primary brand color
        "secondary_color": "#6c757d",    // Secondary brand color
        "custom_logo_url": "",           // URL to your logo image
        "custom_css_enabled": true,      // Enable/disable custom CSS
        "footer_text": "Powered by Your Company",
        "show_custom_header": true       // Show/hide custom header
    }
}
```

## Usage Examples

### Change Colors

Simply edit the `plugin.json` file:

```json
{
    "config": {
        "primary_color": "#e74c3c",      // Red
        "secondary_color": "#3498db"     // Blue
    }
}
```

All buttons, links, and primary elements will automatically use these colors.

### Add Custom Logo

Set the logo URL in configuration:

```json
{
    "config": {
        "custom_logo_url": "https://yourdomain.com/images/logo.png"
    }
}
```

Then use in your Blade templates:

```blade
@themeLogo
```

### Use Theme Colors in Views

Use the custom Blade directives:

```blade
<div style="background-color: @themeColor('primary')">
    Custom colored section
</div>
```

Or access via view composer variables:

```blade
<p style="color: {{ $themeConfig['primaryColor'] }}">
    Themed text
</p>
```

## Customization Beyond Configuration

For more advanced customization:

1. **Add Custom CSS**: Edit `generateCustomCss()` method in the service provider
2. **Override Views**: Create views in `resources/views/` to override portal views
3. **Add Hooks**: Register additional hooks in `registerHooks()` method

## How It Works

This plugin demonstrates several key plugin system features:

1. **Filters**: Uses the `content.render` filter to inject custom CSS
2. **View Composers**: Shares theme configuration with all views
3. **Blade Directives**: Registers custom `@themeColor` and `@themeLogo` directives
4. **Configuration**: Reads settings from `plugin.json`
5. **Service Container**: Registers theme config as a singleton service

## Extending This Plugin

You can extend this plugin by:

- Adding more color options (accent, warning, success colors)
- Implementing a settings page for live editing
- Adding font customization
- Supporting multiple theme presets
- Adding dark mode support

## Example: Creating a Dark Theme

Create a new configuration in `plugin.json`:

```json
{
    "config": {
        "primary_color": "#3b82f6",
        "secondary_color": "#8b5cf6",
        "custom_css_enabled": true,
        "dark_mode": true
    }
}
```

Then modify `generateCustomCss()` to include dark mode styles:

```php
protected function generateCustomCss(): string
{
    $css = "/* Base theme styles */";
    
    if ($this->getConfig('dark_mode', false)) {
        $css .= "
            body {
                background-color: #1a1a1a;
                color: #f0f0f0;
            }
            .card {
                background-color: #2d2d2d;
                border-color: #404040;
            }
        ";
    }
    
    return $css;
}
```

## Best Practices

1. **Keep it simple**: Start with basic color changes before advanced customization
2. **Test thoroughly**: Test theme changes in different browsers
3. **Document changes**: Keep track of what you've customized
4. **Version control**: Keep your plugin under version control
5. **Performance**: Minimize CSS size and complexity

## Troubleshooting

**Colors not applying?**
- Check that `custom_css_enabled` is set to `true`
- Clear browser cache
- Inspect elements to see if CSS is being injected

**Logo not showing?**
- Verify the logo URL is accessible
- Check image permissions
- Ensure you're using the `@themeLogo` directive

## Related Documentation

- Main Plugin Guide: `../PLUGIN_SYSTEM_GUIDE.md`
- Quick Start: `../PLUGIN_QUICK_START.md`
- Example Plugin: `../example-plugin/`

## License

This plugin is provided as an example for the customer portal plugin system.
