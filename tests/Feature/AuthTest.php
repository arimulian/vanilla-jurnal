<?php


use App\Models\User;
use Database\Seeders\UserSeeder;
use Laravel\Sanctum\PersonalAccessToken;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\postJson;
use function Pest\Laravel\seed;

afterEach(function () {
    PersonalAccessToken::where('tokenable_type', User::class)->delete();
    User::query()->delete();
});

test('POST /api/login should return 400 if email or password is wrong', function () {
    $userRequest = ['email' => 'admin@admin.com', 'password' => 'password'];
    postJson('/api/login', [
        'email' => $userRequest['email'],
        'password' => $userRequest['password']
    ])->assertStatus(400)->assertJson([
        'message' => 'email or password is wrong'
    ]);
});

test('login success', function () {
    seed(UserSeeder::class);
    postJson('/api/login', [
        'email' => 'test@test.com',
        'password' => 'test'
    ])->assertStatus(200)
        ->assertJson([
            'user' => [
                'name' => 'test',
                'email' => 'test@test.com',
            ],
        ]);
});


test('User Logout success', function () {
    postJson('/api/logout', [], [
        'authorization' => Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        ),
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
    ])->assertStatus(200)
        ->assertJson([
            'message' => 'Logged out successfully',
        ]);
});

test('User Logout is not authenticate', function () {
    postJson('/api/logout', [], [
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
    ])->assertStatus(401)
        ->assertJson([
            'message' => 'Unauthenticated.',
        ]);
});
