<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Api\SearchController;

Route::get('/admin/login', [AuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

Route::middleware(['auth', \App\Http\Middleware\AdminMiddleware::class])->prefix('admin')->name('admin.')->group(function(){
    Route::get('/search/logs', [DashboardController::class, 'searchLogs'])
         ->name('search.logs');
});
Route::prefix('api')->group(function () {
    // Search endpoints
    Route::get('/search/suggestions', [SearchController::class, 'suggestions']);
    Route::get('/search/logs', [SearchController::class, 'logs']);
    Route::get('/search/analytics', [SearchController::class, 'analytics']);
});

Route::get('/', function () {
    return view('welcome');
});
Route::get('/search', [SearchController::class, 'search']);



