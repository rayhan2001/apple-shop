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

Route::resource('policies', PolicyController::class);

Route::get('list-product-by-category/{id}', [ProductController::class, 'ListProductByCategory']);
Route::get('list-product-by-brand/{id}', [ProductController::class, 'ListProductByBrand']);
Route::get('list-product-by-remark/{remark}', [ProductController::class, 'ListProductByRemark']);

Route::get('list-product-by-slider', [ProductController::class, 'ListProductBySlider']);
Route::get('product-details/{id}', [ProductController::class, 'ProductDetailsById']);
Route::get('list-review-by-product/{product_id}', [ProductController::class, 'ListReviewByProduct']);


// User authentication
Route::post('user-login', [UserController::class, 'UserLogin']);
Route::get('verify-login/{email}/{otp}', [UserController::class, 'VerifyLogin']);
Route::get('logout', [UserController::class, 'UserLogout']);

Route::group(['middleware' => 'TokenAuthentication'], function () {
    // User
    Route::post('create-profile', [ProfileController::class, 'createProfile']);
    Route::get('read-profile', [ProfileController::class, 'readProfile']);

    // Product
    Route::post('product-review', [ProductController::class, 'productReview']);

    // Wishlist
    Route::get('product-wishlist', [ProductController::class, 'productWishlist']);
    Route::get('create-product-wishlist/{product_id}', [ProductController::class, 'createWishlist']);
    Route::get('remove-product-wishlist/{product_id}', [ProductController::class, 'removeWishlist']);

    // Cart
    Route::post('create-cartlist', [ProductController::class, 'createCartList']);
    Route::get('cart-list', [ProductController::class, 'cartList']);
    Route::get('remove-cartlist/{product_id}', [ProductController::class, 'removeCart']);

    // Invoice
    Route::get('create-invoice', [InvoiceController::class, 'createInvoice']);
    Route::get('invoice-list', [InvoiceController::class, 'invoiceList']);
    Route::get('invoice-productlist/{invoice_id}', [InvoiceController::class, 'invoiceProductList']);
});

// payment
Route::post('payment-success', [InvoiceController::class, 'paymentSuccess']);
Route::post('payment-cancel', [InvoiceController::class, 'paymentCancel']);
Route::post('payment-fail', [InvoiceController::class, 'paymentFail']);