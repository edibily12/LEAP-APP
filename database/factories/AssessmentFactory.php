<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Assessment>
 */
class AssessmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = $this->faker->randomElement(['quiz', 'test', 'challenge']);
        $status = $this->faker->randomElement([0]);

        return [
            'type' => $type,
            'status' => $status,
            'total_questions' => random_int(10,30),
            'total_marks' => 20,
            'instructions' => $this->faker->realTextBetween(200,500),
            'start_date' => today()->format('Y-m-d'),
            'start_time' => now()->format('H:i'),
            'expire_date' => today()->addDays(1)->format('Y-m-d'),
            'expire_time' => now()->addHours(3)->format('H:i'),
            'created_by' => "teacher"
        ];
    }
}
