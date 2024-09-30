<?php

use App\Models\Appointment;

dataset('appointment', 
    [
        fn() => Appointment::factory(),
        fn() => Appointment::factory(),
        fn() => Appointment::factory(),
        fn() => Appointment::factory(),
    ]
);