<?php

// Admin routes for plugin management
use App\Http\Controllers\Admin\PluginController;

Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::prefix('plugins')->name('admin.plugins.')->group(function () {
        Route::get('/', [PluginController::class, 'index'])->name('index');
        Route::get('/{name}', [PluginController::class, 'show'])->name('show');
        Route::post('/install', [PluginController::class, 'install'])->name('install');
        Route::post('/{name}/enable', [PluginController::class, 'enable'])->name('enable');
        Route::post('/{name}/disable', [PluginController::class, 'disable'])->name('disable');
        Route::delete('/{name}', [PluginController::class, 'uninstall'])->name('uninstall');
    });
});

// API routes for plugin management
Route::prefix('api/plugins')->middleware(['auth:api'])->group(function () {
    Route::get('/', [PluginController::class, 'apiList']);
    Route::get('/{name}', [PluginController::class, 'apiInfo']);
});
