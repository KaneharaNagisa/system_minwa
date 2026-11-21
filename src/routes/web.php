<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\StoryController;
use Illuminate\Support\Facades\Route;

Route::get('/', [StoryController::class, 'index'])->name('stories.index');
Route::get('/stories/{story}', [StoryController::class, 'show'])->name('stories.show');

Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
Route::post('/admin/login', [AdminController::class, 'login'])->name('admin.login');
Route::post('/admin/logout', [AdminController::class, 'logout'])->name('admin.logout');

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/stories', [AdminController::class, 'stories'])->name('stories.index');
    Route::post('/stories/bulk-tags', [AdminController::class, 'bulkTagStories'])->name('stories.bulk-tags');
    Route::get('/stories/create', [AdminController::class, 'createStory'])->name('stories.create');
    Route::post('/stories', [AdminController::class, 'storeStory'])->name('stories.store');
    Route::get('/stories/{story}/edit', [AdminController::class, 'editStory'])->name('stories.edit');
    Route::put('/stories/{story}', [AdminController::class, 'updateStory'])->name('stories.update');
    Route::delete('/stories/{story}', [AdminController::class, 'destroyStory'])->name('stories.destroy');

    Route::get('/municipalities', [AdminController::class, 'municipalities'])->name('municipalities.index');
    Route::get('/municipalities/create', [AdminController::class, 'createMunicipality'])->name('municipalities.create');
    Route::post('/municipalities', [AdminController::class, 'storeMunicipality'])->name('municipalities.store');
    Route::get('/municipalities/{municipality}/edit', [AdminController::class, 'editMunicipality'])->name('municipalities.edit');
    Route::put('/municipalities/{municipality}', [AdminController::class, 'updateMunicipality'])->name('municipalities.update');
    Route::delete('/municipalities/{municipality}', [AdminController::class, 'destroyMunicipality'])->name('municipalities.destroy');

    Route::get('/tags', [AdminController::class, 'tags'])->name('tags.index');
    Route::get('/tags/create', [AdminController::class, 'createTag'])->name('tags.create');
    Route::post('/tags', [AdminController::class, 'storeTag'])->name('tags.store');
    Route::get('/tags/{tag}/edit', [AdminController::class, 'editTag'])->name('tags.edit');
    Route::put('/tags/{tag}', [AdminController::class, 'updateTag'])->name('tags.update');
    Route::delete('/tags/{tag}', [AdminController::class, 'destroyTag'])->name('tags.destroy');
});
