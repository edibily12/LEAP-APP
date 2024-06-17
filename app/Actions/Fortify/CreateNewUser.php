<?php

namespace App\Actions\Fortify;

use App\Enums\RoleName;
use App\Enums\UserType;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'gender' => ['required'],
            'phone' => ['required', 'regex:/^(0)([1-9])(\d{8})$/'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
//            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ])->validate();

        $user = User::create([
            'name' => $input['name'],
            'phone' => $input['phone'],
            'type' => UserType::STUDENT,
            'email' => $input['email'],
            'password' => Hash::make('1234'),
        ]);

        $user->student()->updateOrCreate([
           'gender' => $input['gender'],
           'dob' => $input['dob'] ?? null,
            'location' => $input['location'] ?? null,
            'language' => $input['language'] ?? null,
            'about' => $input['about'] ?? null,
        ]);

        $user->roles()->sync(Role::whereName(RoleName::STUDENT->value)->first());
        return $user;
    }
}
