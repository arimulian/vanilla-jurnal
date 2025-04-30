<?php

use App\Models\Branch;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;

afterEach(function () {
    // Clean up the database after each test
    \Illuminate\Support\Facades\DB::table('branches')->truncate();
    \Illuminate\Support\Facades\DB::table('users')->truncate();
});

// POST a new branch

test('create branch success', function () {
    $response = postJson('api/branches/create', [
        'name' => 'Branch 1',
        'address' => '123 Main St',
    ], [
        'Authorization' => Sanctum::actingAs(
            \App\Models\User::factory()->create(),
            ['*']
        ),
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
    ]);

    $response->assertStatus(201)
        ->assertJson([
            'message' => 'Branch created successfully',
            'data' => [
                'name' => 'Branch 1',
                'address' => '123 Main St',
            ],
        ]);
});

test('create branch failed', function () {
    $response = postJson('api/branches/create', [
        'name' => '',
        'address' => '',
    ], [
        'Authorization' => Sanctum::actingAs(
            \App\Models\User::factory()->create(),
            ['*']
        ),
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
    ]);
    $response->assertStatus(400)
        ->assertJson([
            'message' => 'Validation failed',
            'errors' => [
                'name' => ['The name field is required.'],
                'address' => ['The address field is required.'],
            ],
        ]);
});

test('create branch duplicate name', function () {
    if (Branch::query()->count() === 0) {
        Branch::query()->create([
            'name' => 'Branch 1',
            'address' => '123 Main St',
        ]);
    }
    $response = postJson('api/branches/create', [
        'name' => 'Branch 1',
        'address' => '123 Main St',
    ], [
        'Authorization' => Sanctum::actingAs(
            \App\Models\User::factory()->create(),
            ['*']
        ),
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
    ]);
    $response->assertStatus(400)
        ->assertJson([
            'message' => 'Validation failed',
            'errors' => [
                'name' => ['The name has already been taken.'],
            ],
        ]);
});

// GET Branch

test('get branches success', function () {
    $branch = Branch::query()->create([
        'name' => 'Branch 1',
        'address' => '123 Main St',
    ]);
    $response = getJson('api/branches/get', [
        'Authorization' => Sanctum::actingAs(
            \App\Models\User::factory()->create(),
            ['*']
        ),
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
    ]);
    $data = Branch::query()->get();
    $response->assertStatus(200)
        ->assertJson([
            'data' => $data->toArray(),
        ]);

    Log::debug('Branch data:', $data->toArray());
});

test('get branch by id success', function () {
    $branch = Branch::query()->create([
        'name' => 'Branch 1',
        'address' => '123 Main St',
    ]);
    $response = getJson('api/branches/get/' . $branch->id, [
        'Authorization' => Sanctum::actingAs(
            \App\Models\User::factory()->create(),
            ['*']
        ),
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
    ]);
    $response->assertStatus(200)
        ->assertJson([
            'data' => [
                'id' => $branch->id,
                'name' => 'Branch 1',
                'address' => '123 Main St',
                'is_active' => 1,
            ],
        ]);
});
