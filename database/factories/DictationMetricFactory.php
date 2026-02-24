<?php

namespace Database\Factories;

use App\Enums\DictationDifficulty;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DictationMetric>
 */
class DictationMetricFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $correctCount = fake()->numberBetween(0, 20);
        $errorCount = fake()->numberBetween(0, 10);
        $missingCount = fake()->numberBetween(0, 5);
        $extraCount = fake()->numberBetween(0, 3);
        $totalWords = $correctCount + $errorCount + $missingCount;
        $accuracy = $totalWords > 0 ? round(($correctCount / $totalWords) * 100, 2) : 0;

        return [
            'student_id' => User::factory(),
            'exercise_id' => fake()->uuid(),
            'difficulty' => fake()->randomElement([
                DictationDifficulty::EASY,
                DictationDifficulty::MEDIUM,
                DictationDifficulty::HARD
            ]),
            'correct_count' => $correctCount,
            'error_count' => $errorCount,
            'missing_count' => $missingCount,
            'extra_count' => $extraCount,
            'accuracy_percent' => $accuracy,
            'letter_omission_count' => fake()->numberBetween(0, 5),
            'letter_insertion_count' => fake()->numberBetween(0, 5),
            'letter_substitution_count' => fake()->numberBetween(0, 5),
            'transposition_count' => fake()->randomFloat(2, 0, 5),
            'split_join_count' => fake()->numberBetween(0, 3),
            'punctuation_error_count' => fake()->numberBetween(0, 3),
            'capitalization_error_count' => fake()->numberBetween(0, 3),
            'error_words' => fake()->randomElements([
                'palavra', 'exemplo', 'teste', 'erro', 'escrita'
            ], fake()->numberBetween(0, 3)),
            'resolution' => fake()->sentence(10),
            'created_at' => fake()->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
