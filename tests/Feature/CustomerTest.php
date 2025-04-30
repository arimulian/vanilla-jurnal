<?php

use App\Models\Customer;
use App\Models\User;

use Laravel\Sanctum\Sanctum;
use Illuminate\Support\Facades\DB;


use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

beforeEach(function () {
    DB::table('users')->truncate();
    DB::table('customers')->truncate();
});



test('Customer create success', function () {
    $response = postJson('/api/customers/create', [
        'name' => 'John Doe',
        'email' => 'test@test.com',
        'phone' => '1234567890',
        'address' => '123 Main St',
    ], [
        'authorization' => Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        ),
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
    ]);

    $customer = Customer::query()->get()->toArray();
    $response->assertStatus(201)
        ->assertJson([
            'message' => 'Customer created successfully',
            'data' => $customer,
        ]);
});

test('Customer create failed', function () {
    $response = postJson('/api/customers/create', [
        'name' => '',
        'email' => 'test@tes.com',
        'phone' => '1234567890',
        'address' => 'test',
    ], [
        'authorization' => Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        ),
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
    ]);

    $response->assertStatus(400)
        ->assertJson([
            'message' => 'validation error',
            'errors' => [
                'name' => ['The name field is required.'],
            ],
        ]);
});


test('Customer create not authorized',   function () {
    $response = postJson('/api/customers/create', [
        'name' => 'John Doe',
        'email' => 'test@test.com',
        'phone' => '1234567890',
        'address' => '123 Main St'
    ], [
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
    ]);
    $response->assertStatus(401)
        ->assertJson([
            'message' => 'Unauthenticated.',
        ]);
});

test('Customer get all data', function () {
    $customer = new Customer();
    if ($customer->query()->count() >= 0) {
        $customer->query()->create([
            'name' => 'John Doe',
            'email' => 'test@test.com',
            'phone' => '1234567890',
            'address' => '123 Main St',
        ]);
    }

    $response = getJson('/api/customers/get', [
        'authorization' => Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        ),
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'data' => $customer->query()->get()->toArray()
        ]);
});

test('Customer update success', function () {
    $customer = new Customer();
    if (!$customer->exists()) {
        $customer->query()->create([
            'name' => 'John Doe',
            'email' => 'test@test.com',
            'phone' => '1234567890',
            'address' => '123 Main St',
        ]);
    }

    $response = putJson('/api/customers/update/1', [
        'name' => 'Jane Doe',
        'email' => 'test@test.com',
        'phone' => '1234567890',
        'address' => '123 Main St',
    ], [
        'authorization' => Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        ),
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
    ]);
    $response->assertStatus(200);
});

test('Customer update not found', function () {
    $response = putJson('/api/customers/update/1', [
        'name' => 'Jane Doe',
        'email' => 'test@test.com',
        'phone' => '1234567890',
        'address' => '123 Main St',
    ], [
        'authorization' => Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        ),
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
    ]);
    $response->assertStatus(404)
        ->assertJson([
            'message' => 'Customer not found',
        ]);
});

test('Customer get by id success', function () {
    $customer = new Customer();
    if (!$customer->exists()) {
        $customer->query()->create([
            'name' => 'John Doe',
            'email' => 'test@test.com',
            'phone' => '1234567890',
            'address' => '123 Main St',
        ]);
    }
    $response = getJson('/api/customers/get/1', [
        'authorization' => Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        ),
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
    ]);
    $response->assertStatus(200)
        ->assertJson([
            'data' => $customer->query()->find(1)->toArray()
        ]);
});
