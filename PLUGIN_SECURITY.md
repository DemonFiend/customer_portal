# Plugin System - Security Considerations

## Overview

When implementing a plugin system, security is paramount. This document outlines security best practices, potential risks, and mitigation strategies for the customer portal plugin system.

## Security Principles

### 1. Principle of Least Privilege
- Plugins should only have access to what they need
- Use permission checks before sensitive operations
- Sandbox plugin execution where possible

### 2. Defense in Depth
- Multiple layers of security
- Validate at plugin load time and runtime
- Don't rely on a single security measure

### 3. Fail Securely
- Disable problematic plugins automatically
- Log security events
- Fail closed (deny by default)

## Common Security Risks

### 1. Code Injection

**Risk:** Malicious plugins could execute arbitrary code.

**Mitigation:**
```php
// In PluginManager.php, add validation
protected function validatePluginCode(string $path): bool
{
    // Check for dangerous functions
    $dangerousFunctions = [
        'eval',
        'exec',
        'shell_exec',
        'system',
        'passthru',
        'proc_open',
        'popen'
    ];
    
    $files = File::allFiles($path . '/src');
    foreach ($files as $file) {
        $content = File::get($file);
        foreach ($dangerousFunctions as $func) {
            if (stripos($content, $func . '(') !== false) {
                Log::warning("Dangerous function '{$func}' found in plugin");
                return false;
            }
        }
    }
    
    return true;
}
```

### 2. SQL Injection

**Risk:** Plugins using raw SQL queries could be vulnerable.

**Mitigation:**
- Always use Eloquent ORM or Query Builder
- Never concatenate user input into SQL
- Use parameter binding

**Example:**
```php
// ❌ DANGEROUS - Never do this
$results = DB::select("SELECT * FROM users WHERE email = '{$email}'");

// ✅ SAFE - Use bindings
$results = DB::select("SELECT * FROM users WHERE email = ?", [$email]);

// ✅ BETTER - Use Query Builder
$results = DB::table('users')->where('email', $email)->get();

// ✅ BEST - Use Eloquent
$results = User::where('email', $email)->get();
```

### 3. Cross-Site Scripting (XSS)

**Risk:** Plugin views could inject malicious scripts.

**Mitigation:**
```blade
{{-- ❌ DANGEROUS - Raw output --}}
{!! $pluginContent !!}

{{-- ✅ SAFE - Escaped output (default) --}}
{{ $pluginContent }}

{{-- ✅ When HTML is needed, sanitize first --}}
{!! clean($pluginContent) !!}
```

Create a sanitization helper:
```php
// In plugin service provider
protected function sanitizeHtml(string $html): string
{
    return strip_tags($html, '<p><br><strong><em><a><ul><ol><li>');
}
```

### 4. Cross-Site Request Forgery (CSRF)

**Risk:** Plugin forms could be vulnerable to CSRF attacks.

**Mitigation:**
```blade
{{-- Always include CSRF token in forms --}}
<form method="POST" action="/plugin-action">
    @csrf
    <!-- form fields -->
</form>
```

```php
// In controllers, use middleware
public function __construct()
{
    $this->middleware('csrf');
}
```

### 5. Path Traversal

**Risk:** Plugins could access files outside their directory.

**Mitigation:**
```php
// Validate paths
protected function validatePluginPath(string $path): bool
{
    $realPath = realpath($path);
    $pluginsPath = realpath(base_path('plugins'));
    
    // Ensure path is within plugins directory
    if (strpos($realPath, $pluginsPath) !== 0) {
        throw new SecurityException('Invalid plugin path');
    }
    
    return true;
}
```

### 6. Unauthorized Access

**Risk:** Plugins could bypass authentication/authorization.

**Mitigation:**
```php
// In plugin routes, always use middleware
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/plugin-page', [PluginController::class, 'index']);
});

// In controllers, check permissions
public function sensitiveAction()
{
    if (!auth()->user()->can('use_plugin_feature')) {
        abort(403, 'Unauthorized');
    }
    
    // Action logic
}
```

## Plugin Validation

### Required Validation Steps

1. **Configuration Validation**
```php
protected function validatePluginConfig(array $config): bool
{
    // Required fields
    $required = ['name', 'version', 'description', 'author'];
    
    foreach ($required as $field) {
        if (!isset($config[$field]) || empty($config[$field])) {
            return false;
        }
    }
    
    // Validate version format
    if (!preg_match('/^\d+\.\d+\.\d+$/', $config['version'])) {
        return false;
    }
    
    // Validate plugin name (alphanumeric and dashes only)
    if (!preg_match('/^[a-z0-9\-]+$/', $config['name'])) {
        return false;
    }
    
    return true;
}
```

2. **Service Provider Validation**
```php
protected function validateServiceProvider(string $class): bool
{
    if (!class_exists($class)) {
        return false;
    }
    
    // Must extend BasePluginServiceProvider
    if (!is_subclass_of($class, BasePluginServiceProvider::class)) {
        Log::warning("Plugin service provider must extend BasePluginServiceProvider");
        return false;
    }
    
    return true;
}
```

3. **Dependency Validation**
```php
protected function validateDependencies(array $dependencies): bool
{
    foreach ($dependencies as $dependency) {
        if (!$this->plugins->has($dependency)) {
            Log::error("Plugin depends on missing plugin: {$dependency}");
            return false;
        }
        
        $dependencyPlugin = $this->plugins->get($dependency);
        if (!$dependencyPlugin['enabled']) {
            Log::error("Plugin depends on disabled plugin: {$dependency}");
            return false;
        }
    }
    
    return true;
}
```

## Secure Plugin Loading

### Load Order Security

```php
public function discoverPlugins(): Collection
{
    // 1. Discover all plugins
    $plugins = $this->findPlugins();
    
    // 2. Validate each plugin
    foreach ($plugins as $key => $plugin) {
        if (!$this->validatePlugin($plugin)) {
            unset($plugins[$key]);
            continue;
        }
    }
    
    // 3. Check dependencies
    foreach ($plugins as $key => $plugin) {
        if (!$this->validateDependencies($plugin['config']['dependencies'] ?? [])) {
            unset($plugins[$key]);
        }
    }
    
    // 4. Load in dependency order
    return $this->loadInOrder($plugins);
}
```

## Data Security

### 1. Encrypt Sensitive Configuration

```php
// In plugin.json, mark sensitive fields
{
    "config": {
        "api_key": {
            "value": "encrypted_value",
            "encrypted": true
        }
    }
}

// In service provider
protected function getConfig(string $key, $default = null)
{
    $value = $this->pluginConfig['config'][$key] ?? $default;
    
    if (is_array($value) && isset($value['encrypted']) && $value['encrypted']) {
        return decrypt($value['value']);
    }
    
    return $value;
}
```

### 2. Secure File Uploads

```php
// Validate file uploads in plugins
protected function validateUpload(UploadedFile $file): bool
{
    // Check file size
    if ($file->getSize() > 5 * 1024 * 1024) { // 5MB
        throw new ValidationException('File too large');
    }
    
    // Check MIME type
    $allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
    if (!in_array($file->getMimeType(), $allowedTypes)) {
        throw new ValidationException('Invalid file type');
    }
    
    // Check file extension
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf'];
    if (!in_array($file->getClientOriginalExtension(), $allowedExtensions)) {
        throw new ValidationException('Invalid file extension');
    }
    
    return true;
}
```

## Hook Security

### Validate Hook Callbacks

```php
public function addAction(string $hook, callable $callback, int $priority = 10): void
{
    // Validate hook name
    if (!preg_match('/^[a-z0-9\._]+$/', $hook)) {
        throw new InvalidArgumentException('Invalid hook name');
    }
    
    // Rate limiting for hooks
    if ($this->getHookCallCount($hook) > 100) {
        Log::warning("Too many callbacks for hook: {$hook}");
        return;
    }
    
    // Store callback
    if (!isset($this->hooks[$hook])) {
        $this->hooks[$hook] = [];
    }
    
    $this->hooks[$hook][] = [
        'callback' => $callback,
        'priority' => $priority
    ];
}
```

### Sandbox Hook Execution

```php
public function doAction(string $hook, ...$args): void
{
    if (!isset($this->hooks[$hook])) {
        return;
    }
    
    foreach ($this->hooks[$hook] as $hookData) {
        try {
            // Execute with timeout
            call_user_func($hookData['callback'], ...$args);
        } catch (\Throwable $e) {
            // Log but don't break execution
            Log::error("Hook execution failed: {$hook}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
```

## API Security

### Rate Limiting

```php
// In plugin routes
Route::middleware(['auth', 'throttle:60,1'])->group(function () {
    Route::get('/plugin/api/data', [PluginApiController::class, 'getData']);
});
```

### API Authentication

```php
// Use Sanctum or Passport for API authentication
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/plugin/api/action', [PluginApiController::class, 'action']);
});
```

## Monitoring and Logging

### Security Event Logging

```php
// Log security events
protected function logSecurityEvent(string $event, array $context = []): void
{
    Log::channel('security')->warning($event, array_merge([
        'user_id' => auth()->id(),
        'ip' => request()->ip(),
        'timestamp' => now(),
    ], $context));
}

// Usage
$this->logSecurityEvent('plugin.unauthorized_access', [
    'plugin' => $pluginName,
    'attempted_action' => $action
]);
```

### Monitor Plugin Activity

```php
// In AppServiceProvider
public function boot(): void
{
    Event::listen('plugin.*', function ($event) {
        Log::info('Plugin event triggered', [
            'event' => get_class($event),
            'data' => $event->getData()
        ]);
    });
}
```

## Security Checklist for Plugin Developers

- [ ] Validate all user input
- [ ] Use parameterized queries or ORM
- [ ] Escape output in views
- [ ] Include CSRF tokens in forms
- [ ] Use authentication middleware
- [ ] Check permissions before sensitive operations
- [ ] Validate file uploads
- [ ] Sanitize HTML content
- [ ] Use HTTPS for external API calls
- [ ] Don't expose sensitive data in responses
- [ ] Log security events
- [ ] Handle errors gracefully
- [ ] Keep dependencies updated
- [ ] Use environment variables for secrets
- [ ] Follow principle of least privilege

## Security Configuration

### Recommended Settings

```php
// In config/plugins.php
return [
    'security' => [
        'validate_signature' => env('PLUGIN_VALIDATE_SIGNATURE', true),
        'allowed_authors' => explode(',', env('PLUGIN_ALLOWED_AUTHORS', '')),
        'require_https_downloads' => true,
        'max_file_size' => 10 * 1024 * 1024, // 10MB
        'code_scanning' => true,
        'auto_disable_on_error' => true,
    ],
];
```

### Environment Variables

```env
# .env
PLUGIN_VALIDATE_SIGNATURE=true
PLUGIN_ALLOWED_AUTHORS=trusted-author,another-author
PLUGIN_DEBUG=false
PLUGIN_SHOW_ERRORS=false
```

## Incident Response

### When a Security Issue is Found

1. **Disable the plugin immediately**
```php
$pluginManager->disablePlugin('vulnerable-plugin');
```

2. **Log the incident**
```php
Log::critical('Security vulnerability in plugin', [
    'plugin' => 'plugin-name',
    'vulnerability' => 'description',
    'action_taken' => 'disabled'
]);
```

3. **Notify administrators**
```php
Mail::to(config('mail.admin'))->send(new SecurityAlert($details));
```

4. **Review plugin code**
5. **Remove if necessary**
6. **Document the incident**

## Regular Security Audits

### Audit Schedule

- **Daily**: Review security logs
- **Weekly**: Check for plugin updates
- **Monthly**: Full security audit of installed plugins
- **Quarterly**: Review access permissions

### Audit Checklist

```php
// Security audit script
php artisan plugin:audit

// Check for:
// - Plugins without updates for 6+ months
// - Plugins from untrusted sources
// - Unused plugins
// - Plugins with known vulnerabilities
// - Excessive permissions
```

## Resources

- [OWASP Top 10](https://owasp.org/www-project-top-ten/)
- [Laravel Security Best Practices](https://laravel.com/docs/security)
- [PHP Security Best Practices](https://www.php.net/manual/en/security.php)

## Summary

Security is an ongoing process, not a one-time task. Key takeaways:

1. **Always validate and sanitize input**
2. **Use built-in security features**
3. **Follow the principle of least privilege**
4. **Log security events**
5. **Keep plugins and dependencies updated**
6. **Regular security audits**
7. **Have an incident response plan**

Remember: A compromised plugin can compromise your entire application. Take security seriously!
