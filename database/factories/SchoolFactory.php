<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\School>
 */
class SchoolFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company() . ' School',
            'address' => fake()->streetAddress() . ', ' . fake()->city(),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->safeEmail(),
            'director_name' => fake()->name(),
        ];
    }
}