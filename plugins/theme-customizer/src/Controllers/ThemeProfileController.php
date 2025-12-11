<?php

namespace Plugins\ThemeCustomizer\Controllers;

use App\Http\Controllers\Controller;
use Plugins\ThemeCustomizer\Models\UserThemePreference;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ThemeProfileController extends Controller
{
    /**
     * Toggle dark mode for authenticated user
     */
    public function toggleDarkMode(Request $request): JsonResponse
    {
        $contactId = auth()->user()->contact_id;
        $darkMode = UserThemePreference::toggleDarkMode($contactId);

        return response()->json([
            'success' => true,
            'dark_mode' => $darkMode,
            'message' => $darkMode ? 'Dark mode enabled' : 'Light mode enabled'
        ]);
    }

    /**
     * Get current theme preference
     */
    public function getThemePreference(Request $request): JsonResponse
    {
        $contactId = auth()->user()->contact_id;
        $preference = UserThemePreference::getForUser($contactId);

        return response()->json([
            'dark_mode' => $preference->dark_mode
        ]);
    }
}
