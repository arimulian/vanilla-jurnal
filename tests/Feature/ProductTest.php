<?php

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\UploadedFile as HttpUploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;
use function PHPUnit\Framework\assertFileExists;

afterEach(function () {
    // Clean up the database after each test
    Category::query()->delete();
    \Illuminate\Support\Facades\DB::table('users')->truncate();
    \Illuminate\Support\Facades\DB::table('products')->truncate();
    Storage::disk('public')->deleteDirectory('products');
});
test('create product success', function () {
    // Create a user
    $user = \App\Models\User::factory()->create();
    $category = Category::query()->create([
        'name' => 'Test Category',
        'category_type' => 'Test Type',
        'slug' => 'test-category',
    ]);

    // Create a product
    $response = $this->actingAs($user)->postJson('/api/products/create', [
        'name' => 'Test Product',
        'description' => 'This is a test product',
        'price' => 100.000,
        'cost_price' => 50.000,
        'stock' => 10,
        'category_id' => $category->id,
        'sku' => 'TESTSKU123',
        'barcode' => '1234567890123',
        'image' => null,
    ]);

    $response->assertStatus(201)
        ->assertJson([
            'message' => 'Product created successfully',
            'product' => [
                'name' => 'Test Product',
                'description' => 'This is a test product',
                'price' => 100.000,
                'cost_price' => 50.000,
                'stock' => 10,
                'category_id' => $category->id,
                'sku' => 'TESTSKU123',
                'barcode' => '1234567890123',
            ],
        ]);
});

test('create product without authentication', function () {
    // Create a product without authentication
    $response = $this->postJson('/api/products/create', [
        'name' => 'Test Product',
        'description' => 'This is a test product',
        'price' => 100.000,
        'cost_price' => 50.000,
        'stock' => 10,
        'category_id' => 1,
        'sku' => 'TESTSKU123',
        'barcode' => '1234567890123',
        'image' => null,
    ]);

    $response->assertStatus(401)
        ->assertJson([
            'message' => 'Unauthenticated.',
        ]);
});

test('create product with image', function () {
    // Create a user
    $user = \App\Models\User::factory()->create();
    $category = Category::query()->create([
        'name' => 'Test Category',
        'category_type' => 'Test Type',
        'slug' => 'test-category',
    ]);

    $file = HttpUploadedFile::fake()->image('test-product.jpg');
    // Create a product with an image
    $response = $this->actingAs($user)->postJson('/api/products/create', [
        'name' => 'Test Product',
        'description' => 'This is a test product',
        'price' => 100.000,
        'cost_price' => 50.000,
        'stock' => 10,
        'category_id' => $category->id,
        'sku' => 'TESTSKU123',
        'barcode' => '1234567890123',
        'image' => $file,
    ]);

    $response->assertStatus(201)
        ->assertJson([
            'message' => 'Product created successfully',
            'product' => [
                'name' => 'Test Product',
                'description' => 'This is a test product',
                'price' => 100.000,
                'cost_price' => 50.000,
                'stock' => 10,
                'category_id' => $category->id,
                'sku' => 'TESTSKU123',
                'barcode' => '1234567890123',
                // Add image path if applicable
            ],
        ]);
    // Check if the image was database
    $this->assertDatabaseHas('products', [
        'name' => 'Test Product',
        'description' => 'This is a test product',
        'price' => 100.000,
        'cost_price' => 50.000,
        'stock' => 10,
        'category_id' => $category->id,
        'sku' => 'TESTSKU123',
        'barcode' => '1234567890123',
        // Add image path if applicable
        'image' => 'products/' . $file->hashName(),
    ]);
    // Check if the image file exists
    assertFileExists(storage_path('app/public/products/' . $file->hashName()));
});

test('create product with invalid data', function () {

    postJson('/api/products/create', [
        'name' => '',
        'description' => '',
        'price' => -100.000,
        'cost_price' => -50.000,
        'stock' => -10,
        'category_id' => 1,
        'sku' => '',
        'barcode' => '',
        'image' => null,
    ], [
        'authorization' => Sanctum::actingAs(
            \App\Models\User::factory()->create(),
            ['*']
        ),
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
    ])->assertStatus(400)
        ->assertJsonValidationErrors([
            'name',
            'price',
            'cost_price',
            'stock',
        ]);
});


test('get all products', function () {
    if (Product::query()->count() === 0) {
        $category = Category::query()->create([
            'name' => 'Test Category',
            'category_type' => 'Test Type',
            'slug' => 'test-category',
        ]);
        Product::query()->create([
            'name' => 'Test Product',
            'description' => 'This is a test product',
            'price' => 100.000,
            'cost_price' => 50.000,
            'stock' => 10,
            'category_id' => $category->id,
            'image' => null,
            'slug' => 'test-product',
            'sku' => 'TESTSKU123',
            'barcode' => '1234567890123',
        ]);
    }
    $response = getJson('/api/products/get', [
        'authorization' => Sanctum::actingAs(
            \App\Models\User::factory()->create(),
            ['*']
        ),
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
    ]);
    $response->assertStatus(200)
        ->assertJson([
            'data' => [
                [
                    'name' => 'Test Product',
                    'description' => 'This is a test product',
                    'price' => 100.000,
                    'cost_price' => 50.000,
                    'stock' => 10,
                    'category_id' => $category->id,
                    'sku' => 'TESTSKU123',
                    'barcode' => '1234567890123',
                ],
            ],
        ]);
    $response->assertJsonCount(1, 'data');
});


test('get product by slug success', function () {
    if (Product::query()->count() === 0) {
        $category = Category::query()->create([
            'name' => 'Test Category',
            'category_type' => 'Test Type',
            'slug' => 'test-category',
        ]);
        Product::query()->create([
            'name' => 'Test Product',
            'description' => 'This is a test product',
            'price' => 100.000,
            'cost_price' => 50.000,
            'stock' => 10,
            'category_id' => $category->id,
            'image' => null,
            'slug' => 'test-product',
            'sku' => 'TESTSKU123',
            'barcode' => '1234567890123',
        ]);
    }
    $response = getJson('api/products/get/test-product', [
        'authorization' => Sanctum::actingAs(
            \App\Models\User::factory()->create(),
            ['*']
        ),
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
    ]);
    $response->assertStatus(200)
        ->assertJson([
            'data' => [
                'name' => 'Test Product',
                'description' => 'This is a test product',
                'price' => 100.000,
                'cost_price' => 50.000,
                'stock' => 10,
                'category_id' => $category->id,
                'sku' => 'TESTSKU123',
                'barcode' => '1234567890123',
            ],
        ]);
});

test('get product by slug is not found', function () {
    $response = getJson('api/products/get/non-existing-product', [
        'authorization' => Sanctum::actingAs(
            \App\Models\User::factory()->create(),
            ['*']
        ),
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
    ]);
    $response->assertStatus(404)
        ->assertJson([
            'message' => 'Product not found',
        ]);
});

test('update product success', function () {
    if (Product::query()->count() === 0) {
        $category = Category::query()->create([
            'name' => 'Test Category',
            'category_type' => 'Test Type',
            'slug' => 'test-category',
        ]);
        Product::query()->create([
            'name' => 'Test Product',
            'description' => 'This is a test product',
            'price' => 100.000,
            'cost_price' => 50.000,
            'stock' => 10,
            'category_id' => $category->id,
            'image' => null,
            'slug' => 'test-product',
            'sku' => 'TESTSKU123',
            'barcode' => '1234567890123',
        ]);
    }
    $response = putJson('api/products/update/test-product', [
        'name' => 'Test Product',
        'description' => 'This is an updated product',
        'price' => 150.000,
        'cost_price' => 75.000,
        'stock' => 20,
        'category_id' => $category->id,
        'sku' => null,
        'barcode' => null,
    ], [
        'authorization' => Sanctum::actingAs(
            \App\Models\User::factory()->create(),
            ['*']
        ),
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
    ]);
    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Product updated successfully',
            'product' => [
                'name' => 'Test Product',
                'description' => 'This is an updated product',
                'price' => 150.000,
                'cost_price' => 75.000,
                'stock' => 20,
                'category_id' => $category->id,
                'sku' => null,
                'barcode' => null,
            ],
        ]);
});

test('update product with image', function () {
    if (Product::query()->count() === 0) {
        $category = Category::query()->create([
            'name' => 'Test Category',
            'category_type' => 'Test Type',
            'slug' => 'test-category',
        ]);
        Product::query()->create([
            'name' => 'Test Product',
            'description' => 'This is a test product',
            'price' => 100.000,
            'cost_price' => 50.000,
            'stock' => 10,
            'category_id' => $category->id,
            'image' => null,
            'slug' => 'test-product',
            'sku' => 'TESTSKU123',
            'barcode' => '1234567890123',
        ]);
    }
    $file = HttpUploadedFile::fake()->image('test-product.jpg');
    $response = putJson('api/products/update/test-product', [
        'name' => 'Test Product',
        'description' => 'This is an updated product',
        'price' => 150.000,
        'cost_price' => 75.000,
        'stock' => 20,
        'category_id' => $category->id,
        'sku' => null,
        'barcode' => null,
        // Add image path if applicable
        'image' => $file,
    ], [
        'authorization' => Sanctum::actingAs(
            \App\Models\User::factory()->create(),
            ['*']
        ),
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
    ]);
    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Product updated successfully',
            // Add image path if applicable
            'product' => [
                'name' => 'Test Product',
                'description' => 'This is an updated product',
                'price' => 150.000,
                'cost_price' => 75.000,
                'stock' => 20,
                'category_id' => $category->id,
                'sku' => null,
                'barcode' => null,
                'image' => 'products/' . $file->hashName(),
            ],
        ]);
});

test('delete product success', function () {
    if (Product::query()->count() === 0) {
        $category = Category::query()->create([
            'name' => 'Test Category',
            'category_type' => 'Test Type',
            'slug' => 'test-category',
        ]);
        Product::query()->create([
            'name' => 'Test Product',
            'description' => 'This is a test product',
            'price' => 100.000,
            'cost_price' => 50.000,
            'stock' => 10,
            'category_id' => $category->id,
            'image' => null,
            'slug' => 'test-product',
            'sku' => 'TESTSKU123',
            'barcode' => '1234567890123',
        ]);
    }
    $response = deleteJson('api/products/delete/test-product', [], [
        'authorization' => Sanctum::actingAs(
            \App\Models\User::factory()->create(),
            ['*']
        ),
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
    ]);
    $response->assertStatus(200)
        ->assertJson([
            'data' => true,
        ]);
});

test('delete product not found', function () {
    $response = deleteJson('api/products/delete/non-existing-product', [], [
        'authorization' => Sanctum::actingAs(
            \App\Models\User::factory()->create(),
            ['*']
        ),
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
    ]);
    $response->assertStatus(404)
        ->assertJson([
            'message' => 'Product not found',
        ]);
});

test('delete product with image', function () {
    if (Product::query()->count() === 0) {
        $category = Category::query()->create([
            'name' => 'Test Category',
            'category_type' => 'Test Type',
            'slug' => 'test-category',
        ]);
        Product::query()->create([
            'name' => 'Test Product',
            'description' => 'This is a test product',
            'price' => 100.000,
            'cost_price' => 50.000,
            'stock' => 10,
            'category_id' => $category->id,
            'image' => null,
            'slug' => 'test-product',
            'sku' => 'TESTSKU123',
            'barcode' => '1234567890123',
        ]);
    }
    $file = HttpUploadedFile::fake()->image('test-product.jpg');
    $response = deleteJson('api/products/delete/test-product', [
        'name' => 'Test Product',
        'description' => 'This is an updated product',
        'price' => 150.000,
        'cost_price' => 75.000,
        'stock' => 20,
        'category_id' => $category->id,
        'sku' => null,
        'barcode' => null,
        // Add image path if applicable
        'image' => $file,
    ], [
        'authorization' => Sanctum::actingAs(
            \App\Models\User::factory()->create(),
            ['*']
        ),
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
    ]);
    $response->assertStatus(200)
        ->assertJson([
            'data' => true,
        ]);
});
