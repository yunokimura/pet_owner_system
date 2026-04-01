<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class AdoptionPet extends Model
{
    use HasFactory;

    protected $fillable = [
        'pet_name',
        'species',
        'gender',
        'breed',
        'description',
        'traits',
        'weight',
        'image',
        'date_of_birth',
        'is_age_estimated',
    ];

    /**
     * Get the pet's weight with "kg" appended.
     */
    public function getWeightAttribute($value)
    {
        return $value ? $value . ' kg' : null;
    }

    /**
     * Get the pet's age from date_of_birth.
     */
    public function getAgeAttribute()
    {
        if (!$this->date_of_birth) {
            return null;
        }

        $birthDate = Carbon::parse($this->date_of_birth);
        $now = Carbon::now();
        $years = $birthDate->diffInYears($now);

        if ($years < 1) {
            $months = $birthDate->diffInMonths($now);
            return $months . ' ' . ($months === 1 ? 'month' : 'months') . ' old';
        }

        return $years . ' ' . ($years === 1 ? 'year' : 'years') . ' old';
    }
}