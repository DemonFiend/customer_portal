<?php

use Illuminate\Support\Facades\Route;
use Plugins\ThemeCustomizer\Controllers\ThemeAdminController;
use Plugins\ThemeCustomizer\Controllers\ThemeProfileController;

// Admin routes for theme customization
Route::get('/admin', [ThemeAdminController::class, 'show'])->name('theme.admin');
Route::get('/admin/auth', [ThemeAdminController::class, 'showAuth'])->name('theme.admin.auth');
Route::post('/admin/auth', [ThemeAdminController::class, 'authenticate'])->name('theme.admin.authenticate');
Route::post('/admin/settings', [ThemeAdminController::class, 'updateSettings'])->name('theme.admin.update');

// SCXP-BetterPortal: Core functions
Route::get('/admin/plugin-creator', [ThemeAdminController::class, 'showPluginCreator'])->name('theme.admin.plugin-creator');
Route::post('/admin/plugin-creator', [ThemeAdminController::class, 'createPlugin'])->name('theme.admin.create-plugin');
Route::post('/admin/restart-server', [ThemeAdminController::class, 'restartServer'])->name('theme.admin.restart-server');

// User profile theme routes (authenticated)
Route::middleware(['auth'])->prefix('portal')->group(function () {
    Route::post('/theme/toggle', [ThemeProfileController::class, 'toggleDarkMode'])->name('theme.toggle');
    Route::get('/theme/preference', [ThemeProfileController::class, 'getThemePreference'])->name('theme.preference');
});
