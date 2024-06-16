<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'description', 'marks', 'assessment_id', 'file', 'file_type',
    ];

    public function answers() : HasMany
    {
        return $this->hasMany(Answer::class);
    }

    public function assessment() : BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function students() : BelongsToMany
    {
        return $this->belongsToMany(Student::class)
            ->withTimestamps()
            ->withPivot(['assignment_id']);
    }
}
