<?php

use App\Enums\EmployeeStatus;
use App\Models\Employee;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Support\Facades\DB;

use function Pest\Laravel\deleteJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\putJson;

beforeEach(function () {
    // Truncate the users and employees tables before each test
    DB::table('users')->truncate();
    DB::table('employees')->truncate();
});

test('create employee success', function () {

    $response = postJson('api/employees/create', [
        'name' => 'John Doe',
        'position' => 'employee',
        'salary' => 50000,
        'hire_date' => '2023-10-01',
        'status' => 'active',
    ], [
        'authorization' => Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        ),
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
    ]);

    $response->assertStatus(201)
        ->assertJson([
            'message' => 'Employee created successfully',
            'data' => [
                'name' => 'John Doe',
                'position' => 'employee',
                'salary' => '50,000.00',
                'hire_date' => '2023-10-01',
                'status' => 'active',
                'created_at' => now()->format('Y-m-d H:i:s'),
                'updated_at' => now()->format('Y-m-d H:i:s'),
            ],
        ]);
});

test('create employee failed', function () {
    $response = postJson('api/employees/create', [
        'name' => '',
        'position' => '',
        'salary' => '',
        'hire_date' => '',
        'status' => '',
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
                'position' => ['The position field is required.'],
                'salary' => ['The salary field is required.'],
                'hire_date' => ['The hire date field is required.'],
                'status' => ['The status field is required.'],
            ],
        ]);
});

test('create employee not authorized', function () {
    $response = postJson('api/employees/create', [
        'name' => 'John Doe',
        'position' => 'employee',
        'salary' => 50000,
        'hire_date' => '2023-10-01',
        'status' => 'active',
    ], [
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
    ]);

    $response->assertStatus(401)
        ->assertJson([
            'message' => 'Unauthenticated.',
        ]);
});

test('get employees success', function () {
    if (Employee::query()->count() === 0) {
        Employee::create([
            'name' => 'John Doe',
            'position' => 'employee',
            'salary' => 50000,
            'hire_date' => '2023-10-01',
            'status' => 'active',
        ]);
    }
    $response = getJson('api/employees/get', [
        'authorization' => Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        ),
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
    ]);
    $employee = Employee::query()->get();
    $response->assertStatus(200)
        ->assertJson([
            'data' => $employee->toArray(),
        ]);
});

test('get no employees found', function () {
    $response = getJson('api/employees/get', [
        'authorization' => Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        ),
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
    ]);
    $response->assertStatus(404)
        ->assertJson([
            'message' => 'No employees found',
        ]);
});

test('update employee success', function () {
    if (Employee::query()->count() === 0) {
        Employee::create([
            'name' => 'John Doe',
            'position' => 'employee',
            'salary' => 50000,
            'hire_date' => '2023-10-01',
            'status' => 'active',
        ]);
    }
    $response = putJson('api/employees/update/1', [
        'name' => 'Jane Doe',
        'position' => 'manager',
        'salary' => 60000,
        'hire_date' => '2023-10-02',
        'status' => EmployeeStatus::Resign->value,
    ], [
        'authorization' => Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        ),
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Employee updated successfully',
            'data' => [
                'name' => 'Jane Doe',
                'position' => 'manager',
                'salary' => '60,000.00',
                'hire_date' => '2023-10-02',
                'status' => 'resign',
            ],
        ]);
});

test('update employee not found', function () {
    $response = putJson('api/employees/update/1', [
        'name' => 'Jane Doe',
        'position' => 'manager',
        'salary' => 60000,
        'hire_date' => '2023-10-02',
        'status' => EmployeeStatus::Resign->value,
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
            'message' => 'Employee not found',
        ]);
});

test('delete employee success', function () {
    if (Employee::query()->count() === 0) {
        Employee::create([
            'name' => 'John Doe',
            'position' => 'employee',
            'salary' => 50000,
            'hire_date' => '2023-10-01',
            'status' => 'active',
        ]);
    }
    $response = deleteJson('api/employees/delete/1', [
        'authorization' => Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        ),
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Employee deleted successfully',
            'data' => true,
        ]);
});
