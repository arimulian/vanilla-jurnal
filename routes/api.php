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
});
