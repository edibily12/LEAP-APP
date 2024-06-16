<?php

namespace Database\Seeders;

use App\Enums\RoleName;
use App\Enums\UserType;
use App\Models\Role;
use App\Models\User;
use App\Traits\WithTruncateTable;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{

    use WithTruncateTable;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->truncate('users');
        $this->createAdminUser();
        $this->createStaffUser();
//        $this->createStudentUser();
//        $this->createStudentUser2();
    }

    public function createAdminUser(): void
    {
        User::create([
            'name' => 'Admin User',
            'type' => UserType::ADMIN->value,
            'email' => 'admin@admin.com',
            'password' => bcrypt('password'),
        ])->roles()->sync(Role::where('name', RoleName::ADMIN->value)->first());
    }

    public function createStaffUser(): void
    {
        User::create([
            'name' => 'Staff User',
            'type' => UserType::STAFF->value,
            'email' => 'staff@staff.com',
            'password' => bcrypt('password'),
        ])->roles()->sync(Role::where('name', RoleName::TEACHER->value)->first());
    }

    public function createStudentUser(): void
    {
        $user = User::create([
            'name' => 'Student User',
            'type' => UserType::STUDENT->value,
            'email' => 'st@st.com',
            'password' => bcrypt('password'),
        ]);

        $user->student()->create([
            'gender' => 'male',
            'dob' => '1990-01-01',
            'location' => 'New York, NY',
            'Language' => 'English',
            'about' => 'I am a student',
        ]);
        $user->roles()->sync(Role::where('name', RoleName::STUDENT->value)->first());
    }

    public function createStudentUser2(): void
    {
        $user = User::create([
            'name' => 'Student User',
            'type' => UserType::STUDENT->value,
            'email' => 'st2@st2.com',
            'password' => bcrypt('password'),
        ]);

        $user->student()->create([
            'gender' => 'male',
            'dob' => '1990-01-01',
            'location' => 'New York, NY',
            'Language' => 'English',
            'about' => 'I am a student',
        ]);
        $user->roles()->sync(Role::where('name', RoleName::STUDENT->value)->first());
    }

}
