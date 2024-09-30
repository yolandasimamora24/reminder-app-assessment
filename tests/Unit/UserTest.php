<?php

use App\Models\User;
use Tests\CreatesUsers;
use Laravel\Sanctum\Sanctum;
use App\Enums\UserEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class, CreatesUsers::class);

it('does not create a user without a first_name field', function (User $user) {
    $user = $this->createUser();
    Sanctum::actingAs($user);
    $response = $this->postJson('/api/user', []);
    $response->assertStatus(422);
})->with('user');

it('can create a user', function (User $user) {
    $user = $this->createUser();

    Sanctum::actingAs($user);

    $attributes = User::factory()->raw();
    $response = $this->postJson('/api/user', $attributes);
    $response->assertStatus(200);
    $attributes = [
        'first_name' => $attributes['first_name'],
        'last_name' => $attributes['last_name'],
        'user_type' => $attributes['user_type'],
        'guest' => $attributes['guest'],
        'email' => $attributes['email'],
    ];
    $this->assertDatabaseHas('users', $attributes);
})->with('user');

it('can fetch a user', function (User $user) {
    $user = $this->createUser();
    Sanctum::actingAs($user);
    $response = $this->getJson("/api/user/");
    $response->assertStatus(200);
})->with('user');

it('can update a user', function (User $user) {
    $user = $this->createUser();

    Sanctum::actingAs($user);
    $newUser = User::factory()->create();
    $updatedUser = [
        'first_name' => 'Updated User',
        'last_name' => 'Updated User',
        'email' => 'updated@user.com',
        'user_type' => 'Admin',
    ];
    $response = $this->postJson("/api/user/{$newUser->email}", $updatedUser);
    $response->assertStatus(200)->assertJson(['message' => UserEnum::USER_UPDATED()]);
    $this->assertDatabaseHas('users', $updatedUser);
})->with('user');

it('can delete a user', function (User $user) {
    $user = $this->createUser();
    Sanctum::actingAs($user);
    $newUser = User::factory()->create();
    $response = $this->deleteJson("/api/user/{$newUser->email}");
    $response->assertStatus(200)->assertJson(['message' => UserEnum::USER_DELETED()]);
    $this->assertCount(2, User::all());
})->with('user');

