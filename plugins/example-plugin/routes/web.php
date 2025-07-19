<?php

use Illuminate\Support\Facades\Route;
use Plugins\ExamplePlugin\Controllers\ExampleController;

Route::prefix('example-plugin')->name('example-plugin.')->group(function () {
    Route::get('/', [ExampleController::class, 'index'])->name('index');
    Route::get('/create', [ExampleController::class, 'create'])->name('create');
    Route::post('/', [ExampleController::class, 'store'])->name('store');
    Route::get('/{id}', [ExampleController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [ExampleController::class, 'edit'])->name('edit');
    Route::put('/{id}', [ExampleController::class, 'update'])->name('update');
    Route::delete('/{id}', [ExampleController::class, 'destroy'])->name('destroy');
});
