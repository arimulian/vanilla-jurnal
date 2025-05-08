<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\EmployeeController;

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

    // Customers
    Route::post('/customers/create', [CustomerController::class, 'create'])
        ->name('customers.create');
    Route::get('/customers/get', [CustomerController::class, 'get'])
        ->name('customers.get');
    Route::get('/customers/get/{id}', [CustomerController::class, 'getById'])
        ->name('customers.getById');
    Route::put('/customers/update/{id}', [CustomerController::class, 'update'])
        ->name('customers.update');
    Route::delete('/customers/delete/{id}', [CustomerController::class, 'delete'])
        ->name('customers.delete');

    // Employees
    Route::post('/employees/create', [EmployeeController::class, 'create'])
        ->name('employees.create');
    Route::get('/employees/get', [EmployeeController::class, 'get'])
        ->name('employees.get');
    Route::get('/employees/get/{id}', [EmployeeController::class, 'getById'])
        ->name('employees.getById');
    Route::put('/employees/update/{id}', [EmployeeController::class, 'update'])
        ->name('employees.update');
    Route::delete('/employees/delete/{id}', [EmployeeController::class, 'delete'])
        ->name('employees.delete');

    // Branches
    Route::post('/branches/create', [BranchController::class, 'create'])
        ->name('branches.create');
    Route::get('/branches/get', [BranchController::class, 'get'])
        ->name('branches.get');
    Route::get('/branches/get/{branchId}', [BranchController::class, 'getById'])
        ->name('branches.getById');
    Route::delete('/branches/delete/{id}', [BranchController::class, 'delete'])
        ->name('branches.delete');
    Route::put('/branches/update/{branchId}', [BranchController::class, 'update'])
        ->name('branches.update');

    // Sales
    Route::post('/sales/create', [SalesController::class, 'store'])
        ->name('sales.create');
    Route::get('/sales/get', [SalesController::class, 'get'])
        ->name('sales.get');
});
