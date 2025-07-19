<?php

use Illuminate\Support\Facades\Route;
use Plugins\MarkdownEditor\Controllers\MarkdownEditorController;

Route::prefix('markdowneditor')->name('markdowneditor.')->group(function () {
    Route::get('/', [MarkdownEditorController::class, 'index'])->name('index');
    Route::get('/editor', [MarkdownEditorController::class, 'editor'])->name('editor');
    Route::get('/demo', function () {
        return view('markdowneditor::demo');
    })->name('demo');
    Route::post('/preview', [MarkdownEditorController::class, 'preview'])->name('preview');
    Route::post('/save', [MarkdownEditorController::class, 'save'])->name('save');
    Route::get('/config', [MarkdownEditorController::class, 'config'])->name('config');
});

Route::get('/test-route', function () {
    return 'Markdowneditor route is working!';
});
