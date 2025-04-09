<?php


use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;


use function Pest\Laravel\postJson;

afterEach(function () {
    PersonalAccessToken::where('tokenable_type', User::class)->delete();
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

test('should return user and token if email and password is correct', function () {
    $user = User::find(1);
    postJson('/api/login', [
        'email' => 'admin@admin.com',
        'password' => 'rahasia'
    ])->assertStatus(200)
        ->assertJson([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ]);
});
