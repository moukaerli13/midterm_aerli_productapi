<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

/*
| apiResource creates the 5 REST routes for each controller:
|   GET     /api/products          -> index
|   POST    /api/products          -> store
|   GET     /api/products/{id}     -> show
|   PUT     /api/products/{id}     -> update
|   DELETE  /api/products/{id}     -> destroy
*/
Route::apiResource('categories', CategoryController::class);

// Serves uploaded product pictures (goes through the API so CORS headers apply)
Route::get('product-images/{filename}', [ProductController::class, 'image'])
    ->where('filename', '[A-Za-z0-9._-]+')
    ->name('products.image');

Route::apiResource('products', ProductController::class);
