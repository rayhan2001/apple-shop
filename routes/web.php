<?php

use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PolicyController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::resource('brands', BrandController::class)->only([
    'index', 'create', 'store', 'update', 'destroy'
]);
Route::resource('categories', CategoryController::class);
Route::resource('invoices', InvoiceController::class);
Route::resource('policies', PolicyController::class);
Route::resource('products', ProductController::class);
Route::resource('profiles', ProfileController::class);