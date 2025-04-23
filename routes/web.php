<?php

use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;

Route::resource('brands', BrandController::class)->only([
    'index', 'create', 'store', 'update', 'destroy'
]);
Route::resource('categories', CategoryController::class);