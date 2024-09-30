<?php

namespace Database\Factories;

use App\Helpers\Helper;
use App\Models\Reminder;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reminder>
 */
class ReminderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * 
     * @var string
     */
    protected $model = Reminder::class;
    
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'prefix' => fake()->sentence(),
            'description' => fake()->sentence(),
            'reminder_date' => Helper::getRandomDate(),
        ];
    }

}
