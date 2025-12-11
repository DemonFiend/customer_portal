<?php

namespace Plugins\ThemeCustomizer\Models;

use Illuminate\Database\Eloquent\Model;

class UserThemePreference extends Model
{
    protected $table = 'user_theme_preferences';

    protected $fillable = [
        'contact_id',
        'dark_mode',
    ];

    protected $casts = [
        'dark_mode' => 'boolean',
    ];

    /**
     * Get or create preference for a user
     */
    public static function getForUser(int $contactId): self
    {
        return self::firstOrCreate(
            ['contact_id' => $contactId],
            ['dark_mode' => false]
        );
    }

    /**
     * Toggle dark mode for a user
     */
    public static function toggleDarkMode(int $contactId): bool
    {
        $preference = self::getForUser($contactId);
        $preference->dark_mode = !$preference->dark_mode;
        $preference->save();

        return $preference->dark_mode;
    }
}
