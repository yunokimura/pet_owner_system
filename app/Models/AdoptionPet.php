<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}