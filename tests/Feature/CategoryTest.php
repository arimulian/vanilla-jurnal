<?php

use App\Models\Category;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\deleteJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\put;
use function Pest\Laravel\putJson;

afterEach(function () {
    // Clean up the database after each test
    Category::query()->delete();
    \Illuminate\Support\Facades\DB::table('users')->truncate();
});

test('create category success', function () {
    $response = postJson('/api/categories/create', [
        'name' => 'Test Category',
        'category_type' => 'Test Type',
    ], [
        'Authorization' =>  Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        ),
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
    ]);

    $response->assertStatus(201)
        ->assertJson([
            'message' => 'Category created successfully',
            'category' => [
                'name' => 'Test Category',
                'category_type' => 'Test Type',
            ],
        ]);
});

test('create category failed', function () {
    $response = postJson('/api/categories/create', [
        'name' => '',
        'category_type' => '',
    ], [
        'Authorization' =>  Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        ),
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
    ]);

    $response->assertStatus(400)
        ->assertJson([
            'message' => 'validation error',
            'data' => [
                'name' => ['The name field is required.'],
                'category_type' => ['The category type field is required.'],
            ],
        ]);
});

test('update category success', function () {
    $category = Category::query()->limit(1)->first();
    if (!$category) {
        $category = Category::query()->create([
            'name' => 'Test Category',
            'category_type' => 'Test Type',
            'slug' => 'test-category',
        ]);
    }

    $response = putJson('/api/categories/update/' . $category->slug, [
        'name' => 'Updated Category',
        'category_type' => 'Updated Type',
    ], [
        'Authorization' =>  Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        ),
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Category updated successfully',
            'category' => [
                'name' => 'Updated Category',
                'category_type' => 'Updated Type',
            ],
        ]);
});

test('delete category success', function () {
    $category = Category::query()->limit(1)->first();
    if (!$category) {
        $category = Category::query()->create([
            'name' => 'Test Category',
            'category_type' => 'Test Type',
            'slug' => 'test-category',
        ]);
    }

    $response = deleteJson('/api/categories/delete/' . $category->slug, [], [
        'Authorization' =>  Sanctum::actingAs(
            User::factory()->create(),
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
