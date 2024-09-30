<?php

namespace Tests;

use App\Models\User;
use App\Models\Provider;
use Illuminate\Support\Facades\Hash;
trait CreatesUsers
{
    protected function login(array $attributes = []): User
    {
        $user = $this->createUser($attributes);

        $this->be($user);

        return $user;
    }

    protected function loginAs(User $user)
    {
        $this->be($user);
    }

    protected function loginAsModerator(array $attributes = []): User
    {
        return $this->login(array_merge($attributes, ['user_type' => 'admin']));
    }

    protected function loginAsAdmin(array $attributes = []): User
    {
        return $this->login(array_merge($attributes, ['user_type' => 'admin']));
    }

    protected function createUser(array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'user_type' => 'Admin',
            'guest' => 0,
            'email' => 'john@example.com',
            'password' => Hash::make('password'),
        ], $attributes));
    }

  
    protected function createUserProvider(array $attributes = []): User
    {
        $userProvider = User::factory()->create(array_merge([
            'first_name' => 'Mila',
            'last_name' => 'Smith',
            'user_type' => 'provider',
            'guest' => 0,
            'email' => 'mila@example.com',
            'password' => Hash::make('password'),
        ], $attributes));

        if($userProvider) {
            Provider::factory()->create(array_merge([
                'user_id' => $userProvider->id,
                'display_name' => 'Mila Smith',
                'bio' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.',
                'npi_number' => '12345',
                'affiliations' => 'Lorem Ipsum',
                'certifications' => 'Lorem Ipsum',
                'memberships' => 'Lorem Ipsum',
                'years_experience' => '3',
                'online' => '1',
                'compact_license' => '1',
            ], $attributes));
        }

        return $userProvider;
    }
}