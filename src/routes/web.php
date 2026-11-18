<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AdminController;
use App\Http\Controllers\StoryController;

Route::get('/', [StoryController::class, 'index'])->name('stories.index');
Route::get('/stories/{story}', [StoryController::class, 'show'])->name('stories.show');

Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
Route::post('/admin/login', [AdminController::class, 'login'])->name('admin.login');
Route::post('/admin/logout', [AdminController::class, 'logout'])->name('admin.logout');
