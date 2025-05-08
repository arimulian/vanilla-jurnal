<?php

use App\Models\Sales;
use App\Models\SalesItem;
use App\Services\SalesService;
use Laravel\Sanctum\Sanctum;
use Illuminate\Support\Facades\Log;

use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;

beforeEach(
    function () {
        \Illuminate\Support\Facades\DB::table('sales')->delete();
        \Illuminate\Support\Facades\DB::table('products')->delete();
        \Illuminate\Support\Facades\DB::table('categories')->delete();
        \Illuminate\Support\Facades\DB::table('branches')->delete();
        \Illuminate\Support\Facades\DB::table('users')->delete();
    }
);
test('create sales success', function () {
    $category = \App\Models\Category::query()->create([
        'name' => 'Test Category',
        'category_type' => 'Test Type'
    ]);
    $product = \App\Models\Product::query()->create([
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

    $branch = \App\Models\Branch::query()->create([
        'name' => 'Branch 1',
        'address' => '123 Main St',
    ]);

    $salesData = [
        'total_amount' => 1000,
        'discount' => 10,
        'tax' => 5,
        'final_amount' => 1000 - (1000 * 10 / 100) + (1000 * 5 / 100),
        'status' => 'paid',
        'payment_method' => 'credit_card',
        'branch_id' => $branch->id,
        'product_id' => $product->id,
        'unit_price' => $product->price,
        'sales_items' => [
            [
                'product_id' => $product->id,
                'quantity' => 1,
                'unit_price' => $product->price,
            ],
        ],
    ];

    $response = $this->postJson('/api/sales/create', $salesData, [
        'Authorization' => 'Bearer ' . Sanctum::actingAs(
            \App\Models\User::factory()->create(),
            ['*']
        ),
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
    ]);

    $response->assertStatus(201)
        ->assertJson([
            'message' => 'Sales created successfully',
            'data' => [
                'total_amount' => 1000,
                'discount' => 10,
                'tax' => 5,
                'status' => 'paid',
                'payment_method' => 'credit_card',
                'branch_id' => $branch->id,
                'final_amount' => 1000 - (1000 * 10 / 100) + (1000 * 5 / 100),
            ],
        ]);

    $this->assertDatabaseHas('sales', [
        'total_amount' => 1000,
        'discount' => 10,
        'tax' => 5,
        'status' => 'paid',
        'payment_method' => 'credit_card',
        'branch_id' => $branch->id,
        'final_amount' => 1000 - (1000 * 10 / 100) + (1000 * 5 / 100),
    ]);

    $this->assertDatabaseHas('sales_items', [
        'sales_id' => Sales::query()->first()->id,
        'product_id' => $product->id,
        'quantity' => 1,
        'unit_price' => $product->price,
        'total_price' => 100,
    ]);

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'stock' => 9, // 10 - 2
    ]);


    // Log::info('Sales created successfully', [
    //     'sales_data' => Sales::query()->first(),
    //     'sales_items' => SalesItem::query()->where('sales_id', Sales::query()->first()->id)->get(),
    // ]);
});

test('create sales failed', function () {
    $response = postJson('/api/sales/create', [], [
        'Authorization' => 'Bearer ' . Sanctum::actingAs(
            \App\Models\User::factory()->create(),
            ['*']
        ),
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
    ]);
    $response->assertStatus(400)
        ->assertJsonValidationErrors([
            'total_amount',
            'discount',
            'tax',
            'status',
            'payment_method',
            'branch_id',
            'sales_items'
        ]);
});

test('get sales success', function () {
    $category = \App\Models\Category::query()->create([
        'name' => 'Test Category',
        'category_type' => 'Test Type'
    ]);
    $product = \App\Models\Product::query()->create([
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

    $branch = \App\Models\Branch::query()->create([
        'name' => 'Branch 1',
        'address' => '123 Main St',
    ]);

    $salesData = [
        'total_amount' => 1000,
        'discount' => 10,
        'tax' => 5,
        'final_amount' => 1000 - (1000 * 10 / 100) + (1000 * 5 / 100),
        'status' => 'paid',
        'payment_method' => 'credit_card',
        'branch_id' => $branch->id,
        'product_id' => $product->id,
        'unit_price' => $product->price,
        'sales_items' => [
            [
                'product_id' => $product->id,
                'quantity' => 1,
                'unit_price' => $product->price,
            ],
        ],
    ];

    Sales::create($salesData);

    $response = getJson('/api/sales/get',  [
        'Authorization' => 'Bearer ' . Sanctum::actingAs(
            \App\Models\User::factory()->create(),
            ['*']
        ),
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
    ]);

    $salesItems = Sales::with(['salesItems.product', 'branch'])->get()->toArray();

    $response->assertStatus(200)
        ->assertJson([
            'data' => $salesItems,
        ]);

    Log::info('GET sales', [
        'sales' => $salesItems
    ]);
});

// $sales = app(SalesService::class);

// test('test function get sales', function () use ($sales) {
//     $salesItems = $sales->getAllSales();
//     expect($salesItems)->not()->toBeNull()->and(SalesItem::count())->toBe(1);
// });
