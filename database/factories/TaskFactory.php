<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
            'title' => fake()->sentence(),
            'description' => fake()->sentence(),
            'status' => 0,
            'user_id' => fake()->uuid(),
            'deadline' =>Carbon::now()->addDay(10),
            'estimated_effort' => fake()->randomNumber([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]),
            'is_today_task' => false,
            'output' => fake()->sentence(),
        ];
    }
}
