<?php

use App\Models\User;

dataset('user', 
    [
        fn() => User::factory()->create(['first_name' => 'Nuno', 'last_name' => 'Maduro', 'email' => 'nuno@maduro.com']),
        fn() => User::factory()->create(['first_name' => 'Luke', 'last_name' => 'Downing', 'email' => 'luke@downing.com']),
        fn() => User::factory()->create(['first_name' => 'Freek', 'last_name' => 'Van Der Herten', 'email' => 'freek@vanderherten.com']),
    ]
);