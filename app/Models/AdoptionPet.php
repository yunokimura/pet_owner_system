<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Carbon\Carbon;

class AdoptionPet extends Model
{
    use HasFactory;

    protected $primaryKey = 'adoption_id';
    public $incrementing = true;

    protected $fillable = [
        'pet_name',
        'species',
        'gender',
        'breed',
        'description',
        'weight',
        'image',
        'date_of_birth',
        'is_age_estimated',
    ];

    protected $appends = ['age'];

    public function traits(): BelongsToMany
    {
        return $this->belongsToMany(AdoptionTrait::class, 'pet_traits', 'adoption_id', 'trait_id')
            ->withTimestamps();
    }

    public function applications(): BelongsToMany
    {
        return $this->belongsToMany(AdoptionApplication::class, 'adoption_selected_pets', 'adoption_pet_id', 'adoption_application_id')
                    ->withTimestamps();
    }

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'model');
    }

    public function getWeightAttribute($value)
    {
        return $value ? $value . ' kg' : null;
    }

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