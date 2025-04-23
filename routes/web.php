<?php

use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PolicyController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::resource('brands', BrandController::class)->only([
    'index', 'create', 'store', 'update', 'destroy'
]);
Route::resource('categories', CategoryController::class);
Route::resource('invoices', InvoiceController::class);
Route::resource('policies', PolicyController::class);

Route::get('list-product-by-category/{id}', [ProductController::class, 'ListProductByCategory']);
Route::get('list-product-by-brand/{id}', [ProductController::class, 'ListProductByBrand']);
Route::get('list-product-by-remark/{remark}', [ProductController::class, 'ListProductByRemark']);

Route::get('list-product-by-slider', [ProductController::class, 'ListProductBySlider']);
Route::get('product-details/{id}', [ProductController::class, 'ProductDetailsById']);
Route::get('list-review-by-product/{product_id}', [ProductController::class, 'ListReviewByProduct']);

Route::resource('profiles', ProfileController::class);

// User authentication
Route::post('user-login', [UserController::class, 'UserLogin']);
Route::get('verify-login/{email}/{otp}', [UserController::class, 'VerifyLogin']);
Route::get('logout', [UserController::class, 'UserLogout']);
