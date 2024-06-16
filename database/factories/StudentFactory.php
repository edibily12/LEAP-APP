<?php

namespace Database\Factories;

use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{

    protected $model = Student::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(), // Create a new user and use its ID
            'gender' => $this->faker->randomElement(['male', 'female']),
            'dob' => $this->faker->date(),
            'location' => $this->faker->city,
            'Language' => $this->faker->randomElement(['English', 'Spanish', 'French']),
            'about' => $this->faker->paragraph,
        ];
    }
}
