<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login']);
Route::get('/unauthenticated', [AuthController::class, 'unauthenticated'])->name('login');
Route::middleware('auth:sanctum')->group(function () {
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');

    // Categories
    Route::get('/categories', [CategoryController::class, 'get'])
        ->name('categories.get');
    Route::post('/categories/create', [CategoryController::class, 'create'])
        ->name('categories.create');
    Route::put('/categories/update/{category:slug}', [CategoryController::class, 'update'])
        ->name('categories.update');
    Route::delete('/categories/delete/{category:slug}', [CategoryController::class, 'delete'])
        ->name('categories.delete');
    // Products
    Route::post('/products/create', [ProductController::class, 'create'])
        ->name('products.create');
    Route::get('/products/get', [ProductController::class, 'get'])
        ->name('products.get');
    Route::get('/products/get/{slug}', [ProductController::class, 'getBySlug'])
        ->name('products.getBySlug');
    Route::put('/products/update/{slug}', [ProductController::class, 'update'])
        ->name('products.update');
    Route::delete('/products/delete/{slug}', [ProductController::class, 'delete'])
        ->name('products.delete');
});
