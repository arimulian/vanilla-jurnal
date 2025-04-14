<?php

use App\Models\Category;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\postJson;

afterEach(function () {
    // Clean up the database after each test
    Category::query()->delete();
    \Illuminate\Support\Facades\DB::table('users')->truncate();
    \Illuminate\Support\Facades\DB::table('products')->truncate();
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
