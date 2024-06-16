<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Assessment extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'type', 'status', 'total_questions', 'total_marks', 'instructions', 'shuffled', 'start_date', 'start_time', 'expire_date', 'expire_time', 'created_by', 'tag', 'reading_type', 'source'
    ];


    public static function search($search)
    {
        return empty($search) ? static::query() : static::query()
            ->where('type', 'like', '%' . $search . '%');
    }

    public function questions() : HasMany
    {
        return $this->hasMany(Question::class);
    }

    public function reports() : HasMany
    {
        return $this->hasMany(AssessmentReport::class);
    }
}
