<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\RoleName;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use SoftDeletes;
    use TwoFactorAuthenticatable;
    use HasSlug;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'type',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    //generate username slug
    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('username')
            ->slugsShouldBeNoLongerThan(20)
            ->usingSeparator('_');
    }

    //search users
    public static function search($search = '')
    {
        return empty($search) ? static::query() : static::query()
            ->where('name', 'LIKE', "%{$search}%")
            ->orWhere('email', 'LIKE', "%{$search}%")
            ->orWhere('username', 'LIKE', "%{$search}%");
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class)
            ->withTimestamps();
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(RoleName::ADMIN);
    }

    public function isTeacher(): bool
    {
        return $this->hasRole(RoleName::TEACHER);
    }

    public function isStudent(): bool
    {
        return $this->hasRole(RoleName::STUDENT);
    }

    public function isAcademic(): bool
    {
        return $this->hasRole(RoleName::ACADEMIC);
    }

    public function hasRole(RoleName $role): bool
    {
        return $this->roles()->where('name', $role->value)->exists();
    }

    public function permissions(): array
    {
        return $this->roles()->with('permissions')->get()
            ->map(function ($role){
                return $role->permissions->pluck('name');
            })->flatten()->values()->unique()->toArray();
    }

    public function hasPermission($permission): bool
    {
        return in_array($permission, $this->permissions(), true);
    }

    public function student(): HasOne
    {
        return $this->hasOne(Student::class);
    }

}
