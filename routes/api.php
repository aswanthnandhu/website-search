<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SearchController;


Route::get('/search', [SearchController::class, 'search']);
Route::get('/search/suggestions', [SearchController::class, 'suggestions']);