<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\CreatesUsers;
use App\Enums\AuthEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class, CreatesUsers::class);

it('user can login', function ($user) {
    $user = $this->createUser();
    
    $credentials = [
        'email' => $user['email'],
        'password' => 'password'
    ];

    $response = $this->postJson('/api/auth/login', $credentials);
    $response->assertStatus(200);
})->with('user');

it('user can logout', function ($user) {
    $user = $this->createUser();


    Sanctum::actingAs($user);
    $credentials = [
        'email' => $user['email'],
        'password' => 'password'
    ];

    $response = $this->postJson('/api/auth/logout', $credentials);
    $response->assertStatus(200);
})->with('user');


it('can reset password', function ($user) {
    $user = $this->createUser();

    Sanctum::actingAs($user);
    $newUser = User::factory()->create();
    $updatedPassword = [
        'email' => $newUser->email,
        'password' => 'password',
        'new_password' => 'updatedpassword',
    ];
    $response = $this->postJson("/api/auth/update-password", $updatedPassword);
    $response->assertStatus(200)->assertJson(['message' => AuthEnum::USER_PASSWORD_UPDATED()]);
})->with('user');