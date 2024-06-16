<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
      'user_id', 'gender', 'dob', 'location', 'language', 'about', 'level'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function questions() : BelongsToMany
    {
        return $this->belongsToMany(Question::class)
            ->withTimestamps()
            ->withPivot(['assignment_id']);
    }

    public function reports() : HasMany
    {
        return $this->hasMany(AssessmentReport::class);
    }

    public function identification_attempts() : HasMany
    {
        return $this->hasMany(IdentificationAttempt::class);
    }
}
