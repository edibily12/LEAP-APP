<?php

namespace Database\Seeders;

use App\Models\Assessment;
use App\Models\Question;
use App\Models\Student;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
//        $user = User::factory()->create();
//
//        $student = Student::factory()->create([
//            'user_id' => $user->id,
//        ]);

//        User::factory()->create([
//            'name' => 'Test User',
//            'email' => 'test@test.com',
//        ]);

        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
        ]);
//
//        Assessment::factory(3)->create();
//        Question::factory(50)->hasAnswers(4)->create();
//        Question::factory(10)->hasAnswers(2)->create();
    }
}
