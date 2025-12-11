# Plugin Hooks and Filters Reference

This document provides a comprehensive reference for all hooks and filters available in the customer portal plugin system.

## Understanding Hooks vs Filters

### Action Hooks
Action hooks allow you to execute code at specific points without modifying data. They're perfect for:
- Logging events
- Sending notifications
- Triggering side effects
- Running background tasks

**Example:**
```php
app('plugin.hooks')->addAction('user.after_login', function ($user) {
    Log::info("User {$user->id} logged in");
});
```

### Filters
Filters allow you to modify data before it's used. They're perfect for:
- Modifying content
- Changing configuration
- Transforming data
- Adding or removing items from collections

**Example:**
```php
app('plugin.hooks')->addFilter('content.render', function ($content) {
    return $content . '<p>Additional content</p>';
});
```

## Priority System

Both hooks and filters support priorities (lower numbers execute first):

```php
// This runs first (priority 5)
app('plugin.hooks')->addAction('event', $callback1, 5);

// This runs second (priority 10, default)
app('plugin.hooks')->addAction('event', $callback2, 10);

// This runs last (priority 20)
app('plugin.hooks')->addAction('event', $callback3, 20);
```

## Core Application Hooks

### Application Lifecycle

#### `app.booted`
**When:** After the application has fully booted
**Parameters:** None
**Use Case:** Initialize plugin services, register listeners

```php
app('plugin.hooks')->addAction('app.booted', function () {
    // Initialize your plugin
    app('my.service')->initialize();
});
```

#### `app.terminating`
**When:** Before the application terminates
**Parameters:** None
**Use Case:** Cleanup, save state, close connections

```php
app('plugin.hooks')->addAction('app.terminating', function () {
    // Cleanup
    app('my.service')->cleanup();
});
```

## Authentication Hooks

### `user.before_login`
**When:** Before user authentication is processed
**Parameters:** `$credentials` (array)
**Use Case:** Validate credentials, block logins, log attempts

```php
app('plugin.hooks')->addAction('user.before_login', function ($credentials) {
    if (BlockedUsers::isBlocked($credentials['email'])) {
        throw new AuthenticationException('User is blocked');
    }
});
```

### `user.after_login`
**When:** After a user successfully logs in
**Parameters:** `$user` (User model)
**Use Case:** Log activity, send welcome email, update last login

```php
app('plugin.hooks')->addAction('user.after_login', function ($user) {
    Log::info("User logged in: {$user->email}");
    $user->update(['last_login_at' => now()]);
    
    // Send notification
    Mail::to($user)->send(new LoginNotification($user));
});
```

### `user.before_logout`
**When:** Before a user logs out
**Parameters:** `$user` (User model)
**Use Case:** Save session data, log activity

```php
app('plugin.hooks')->addAction('user.before_logout', function ($user) {
    SessionActivity::logLogout($user);
});
```

### `user.after_logout`
**When:** After a user has logged out
**Parameters:** `$user` (User model)
**Use Case:** Clear custom caches, cleanup

```php
app('plugin.hooks')->addAction('user.after_logout', function ($user) {
    Cache::forget("user.{$user->id}.preferences");
});
```

### `user.registered`
**When:** After a new user is registered
**Parameters:** `$user` (User model)
**Use Case:** Send welcome email, create user profile, setup defaults

```php
app('plugin.hooks')->addAction('user.registered', function ($user) {
    UserProfile::create(['user_id' => $user->id]);
    Mail::to($user)->send(new WelcomeEmail());
});
```

## Content Hooks

### `content.before_save`
**When:** Before content is saved to database
**Parameters:** `$content` (Content model)
**Use Case:** Validate, sanitize, transform content

```php
app('plugin.hooks')->addAction('content.before_save', function ($content) {
    $content->slug = Str::slug($content->title);
});
```

### `content.after_save`
**When:** After content is saved to database
**Parameters:** `$content` (Content model)
**Use Case:** Clear caches, trigger updates, index for search

```php
app('plugin.hooks')->addAction('content.after_save', function ($content) {
    Cache::forget("content.{$content->id}");
    SearchIndex::update($content);
});
```

### `content.before_delete`
**When:** Before content is deleted
**Parameters:** `$content` (Content model)
**Use Case:** Cleanup related data, backup

```php
app('plugin.hooks')->addAction('content.before_delete', function ($content) {
    ContentBackup::create($content);
});
```

### `content.after_delete`
**When:** After content is deleted
**Parameters:** `$contentId` (int)
**Use Case:** Cleanup, update statistics

```php
app('plugin.hooks')->addAction('content.after_delete', function ($contentId) {
    Cache::forget("content.{$contentId}");
});
```

## Payment Hooks

### `payment.before_process`
**When:** Before payment is processed
**Parameters:** `$payment` (array with payment data)
**Use Case:** Validate payment, check limits, apply discounts

```php
app('plugin.hooks')->addAction('payment.before_process', function ($payment) {
    if ($payment['amount'] > 10000) {
        throw new PaymentException('Amount exceeds limit');
    }
});
```

### `payment.processing`
**When:** While payment is being processed
**Parameters:** `$payment` (array with payment data)
**Use Case:** Show processing status, log attempts

```php
app('plugin.hooks')->addAction('payment.processing', function ($payment) {
    PaymentLog::create([
        'user_id' => $payment['user_id'],
        'amount' => $payment['amount'],
        'status' => 'processing'
    ]);
});
```

### `payment.processed`
**When:** After payment is successfully processed
**Parameters:** `$payment` (Payment model), `$response` (Gateway response)
**Use Case:** Send receipt, update balances, trigger fulfillment

```php
app('plugin.hooks')->addAction('payment.processed', function ($payment, $response) {
    Mail::to($payment->user)->send(new PaymentReceipt($payment));
    CustomerAccount::credit($payment->user_id, $payment->amount);
});
```

### `payment.failed`
**When:** After payment fails
**Parameters:** `$payment` (array), `$error` (string)
**Use Case:** Log failure, notify user, retry logic

```php
app('plugin.hooks')->addAction('payment.failed', function ($payment, $error) {
    Log::error("Payment failed: {$error}", $payment);
    Mail::to($payment['user'])->send(new PaymentFailedEmail($error));
});
```

## Ticket/Support Hooks

### `ticket.created`
**When:** After a support ticket is created
**Parameters:** `$ticket` (Ticket model)
**Use Case:** Send notifications, assign ticket, log creation

```php
app('plugin.hooks')->addAction('ticket.created', function ($ticket) {
    Mail::to($ticket->user)->send(new TicketCreatedEmail($ticket));
    TicketAssignment::autoAssign($ticket);
});
```

### `ticket.updated`
**When:** After a ticket is updated
**Parameters:** `$ticket` (Ticket model)
**Use Case:** Notify watchers, log changes

```php
app('plugin.hooks')->addAction('ticket.updated', function ($ticket) {
    foreach ($ticket->watchers as $watcher) {
        Mail::to($watcher)->send(new TicketUpdatedEmail($ticket));
    }
});
```

### `ticket.closed`
**When:** After a ticket is closed
**Parameters:** `$ticket` (Ticket model)
**Use Case:** Send satisfaction survey, update metrics

```php
app('plugin.hooks')->addAction('ticket.closed', function ($ticket) {
    Mail::to($ticket->user)->send(new SatisfactionSurvey($ticket));
    TicketMetrics::recordResolution($ticket);
});
```

## Core Filters

### `content.render`
**When:** Before content is rendered to the page
**Parameters:** `$content` (string)
**Return:** Modified content (string)
**Use Case:** Add custom elements, transform content, inject scripts

```php
app('plugin.hooks')->addFilter('content.render', function ($content) {
    // Add custom footer to all content
    return $content . '<div class="custom-footer">Custom Footer</div>';
});
```

### `content.sanitize`
**When:** Before content is saved (sanitization phase)
**Parameters:** `$content` (string)
**Return:** Sanitized content (string)
**Use Case:** Remove dangerous HTML, clean input

```php
app('plugin.hooks')->addFilter('content.sanitize', function ($content) {
    return strip_tags($content, '<p><b><i><a>');
});
```

## Menu Filters

### `menu.items`
**When:** When menu is being built
**Parameters:** `$items` (array of menu items)
**Return:** Modified menu items (array)
**Use Case:** Add custom menu items, reorder, remove items

```php
app('plugin.hooks')->addFilter('menu.items', function ($items) {
    $items[] = [
        'label' => 'Custom Page',
        'url' => '/custom-page',
        'icon' => 'fa-star',
        'order' => 100
    ];
    return $items;
});
```

### `menu.user_items`
**When:** When user menu (profile dropdown) is built
**Parameters:** `$items` (array), `$user` (User model)
**Return:** Modified menu items (array)

```php
app('plugin.hooks')->addFilter('menu.user_items', function ($items, $user) {
    $items[] = [
        'label' => 'My Custom Settings',
        'url' => '/my-settings',
        'icon' => 'fa-cog'
    ];
    return $items;
}, 10);
```

## Permission Filters

### `user.permissions`
**When:** When user permissions are checked
**Parameters:** `$permissions` (array), `$user` (User model)
**Return:** Modified permissions (array)
**Use Case:** Add dynamic permissions, role-based access

```php
app('plugin.hooks')->addFilter('user.permissions', function ($permissions, $user) {
    if ($user->is_premium) {
        $permissions[] = 'access_premium_features';
    }
    return $permissions;
});
```

### `role.permissions`
**When:** When role permissions are loaded
**Parameters:** `$permissions` (array), `$role` (Role model)
**Return:** Modified permissions (array)

```php
app('plugin.hooks')->addFilter('role.permissions', function ($permissions, $role) {
    if ($role->name === 'admin') {
        $permissions[] = 'plugin_management';
    }
    return $permissions;
});
```

## Payment Filters

### `payment.amount`
**When:** Before payment amount is processed
**Parameters:** `$amount` (float), `$user` (User model)
**Return:** Modified amount (float)
**Use Case:** Apply discounts, fees, adjustments

```php
app('plugin.hooks')->addFilter('payment.amount', function ($amount, $user) {
    if ($user->is_premium) {
        return $amount * 0.95; // 5% discount
    }
    return $amount;
});
```

### `payment.gateway`
**When:** When selecting payment gateway
**Parameters:** `$gateway` (string), `$payment` (array)
**Return:** Selected gateway name (string)

```php
app('plugin.hooks')->addFilter('payment.gateway', function ($gateway, $payment) {
    if ($payment['amount'] > 1000) {
        return 'enterprise_gateway'; // Use different gateway for large amounts
    }
    return $gateway;
});
```

## Email Filters

### `email.content`
**When:** Before email is sent
**Parameters:** `$content` (string), `$type` (string)
**Return:** Modified email content (string)

```php
app('plugin.hooks')->addFilter('email.content', function ($content, $type) {
    // Add signature to all emails
    return $content . "\n\n--\nBest regards,\nYour Company Team";
});
```

### `email.subject`
**When:** Before email subject is set
**Parameters:** `$subject` (string), `$type` (string)
**Return:** Modified subject (string)

```php
app('plugin.hooks')->addFilter('email.subject', function ($subject, $type) {
    return "[Customer Portal] {$subject}";
});
```

## View Filters

### `view.data`
**When:** Before view is rendered
**Parameters:** `$data` (array), `$viewName` (string)
**Return:** Modified view data (array)

```php
app('plugin.hooks')->addFilter('view.data', function ($data, $viewName) {
    $data['customVar'] = 'Custom Value';
    return $data;
});
```

### `layout.scripts`
**When:** When page scripts are being loaded
**Parameters:** `$scripts` (array)
**Return:** Modified scripts array

```php
app('plugin.hooks')->addFilter('layout.scripts', function ($scripts) {
    $scripts[] = asset('plugins/my-plugin/js/custom.js');
    return $scripts;
});
```

### `layout.styles`
**When:** When page styles are being loaded
**Parameters:** `$styles` (array)
**Return:** Modified styles array

```php
app('plugin.hooks')->addFilter('layout.styles', function ($styles) {
    $styles[] = asset('plugins/my-plugin/css/custom.css');
    return $styles;
});
```

## Footer Filters

### `footer.text`
**When:** When footer text is rendered
**Parameters:** `$text` (string)
**Return:** Modified footer text (string)

```php
app('plugin.hooks')->addFilter('footer.text', function ($text) {
    return "Copyright © " . date('Y') . " Your Company";
});
```

### `footer.links`
**When:** When footer links are rendered
**Parameters:** `$links` (array)
**Return:** Modified links array

```php
app('plugin.hooks')->addFilter('footer.links', function ($links) {
    $links[] = [
        'label' => 'Privacy Policy',
        'url' => '/privacy'
    ];
    return $links;
});
```

## How to Trigger Custom Hooks

In your plugin or core modifications, you can trigger custom hooks:

### Trigger an Action
```php
// Trigger a custom action
app('plugin.hooks')->doAction('my_plugin.custom_event', $data);

// With multiple parameters
app('plugin.hooks')->doAction('my_plugin.save_complete', $item, $user, $timestamp);
```

### Apply a Filter
```php
// Apply a custom filter
$modifiedValue = app('plugin.hooks')->applyFilters('my_plugin.process_value', $value);

// With additional context
$result = app('plugin.hooks')->applyFilters('my_plugin.calculate', $amount, $user, $type);
```

## Best Practices

1. **Use Descriptive Names**: Use dot notation: `module.action` (e.g., `user.after_login`)
2. **Document Your Hooks**: If creating custom hooks, document them
3. **Check Hook Existence**: Use `hasHook()` and `hasFilter()` before triggering
4. **Handle Priority**: Use priority for hook execution order
5. **Return Modified Data**: Always return filtered data in filters
6. **Error Handling**: Wrap hook callbacks in try-catch when appropriate
7. **Performance**: Keep hook callbacks lightweight
8. **Type Hints**: Use type hints for parameters when possible

## Example: Complete Plugin with Hooks

```php
<?php

namespace Plugins\MyPlugin;

use App\Providers\BasePluginServiceProvider;

class MyPluginServiceProvider extends BasePluginServiceProvider
{
    protected string $pluginName = 'my-plugin';

    protected function registerHooks(): void
    {
        $hooks = app('plugin.hooks');

        // Actions
        $hooks->addAction('user.after_login', [$this, 'handleUserLogin'], 10);
        $hooks->addAction('payment.processed', [$this, 'handlePayment'], 10);
        
        // Filters
        $hooks->addFilter('menu.items', [$this, 'addMenuItems'], 10);
        $hooks->addFilter('content.render', [$this, 'modifyContent'], 10);
    }

    public function handleUserLogin($user): void
    {
        Log::info("Plugin: User {$user->id} logged in");
    }

    public function handlePayment($payment, $response): void
    {
        // Custom payment processing
    }

    public function addMenuItems($items): array
    {
        $items[] = ['label' => 'My Plugin', 'url' => '/my-plugin'];
        return $items;
    }

    public function modifyContent($content): string
    {
        return $content . '<p>Added by My Plugin</p>';
    }
}
```

## Testing Hooks

```php
// Check if a hook exists
if (app('plugin.hooks')->hasHook('user.after_login')) {
    // Hook is registered
}

// Get all registered hooks
$allHooks = app('plugin.hooks')->getHooks();

// Get all registered filters
$allFilters = app('plugin.hooks')->getFilters();

// Remove a hook
app('plugin.hooks')->removeAction('hook.name', $callback);

// Remove a filter
app('plugin.hooks')->removeFilter('filter.name', $callback);
```

## Further Reading

- Plugin Quick Start: `PLUGIN_QUICK_START.md`
- Plugin System Guide: `PLUGIN_SYSTEM_GUIDE.md`
- Example Plugins: `plugins/example-plugin/` and `plugins/theme-customizer/`
