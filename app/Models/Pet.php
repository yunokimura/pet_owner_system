<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Pet extends Model
{
    use HasFactory;

    protected $primaryKey = 'pet_id';

    protected $fillable = [
        'owner_id',
        'pet_name',
        'species',
        'breed',
        'sex',
        'birthdate',
        'pet_image',
        'is_neutered',
        'is_crossbreed',
        'estimated_age',
        'pet_weight',
        'body_mark_image',
        'body_mark_details',
        'training',
        'insurance',
        'behavior',
        'likes',
        'dislikes',
        'diet',
        'allergy',
    ];

    protected $casts = [
        'birthdate' => 'datetime',
        'training' => 'array',
        'insurance' => 'array',
        'behavior' => 'array',
        'likes' => 'array',
        'dislikes' => 'array',
        'diet' => 'array',
        'allergy' => 'array',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(PetOwner::class, 'owner_id', 'owner_id');
    }

    public function medicalRecords(): BelongsToMany
    {
        return $this->belongsToMany(MedicalRecord::class, 'medical_record_pets', 'pet_id', 'medical_record_id')
                    ->withTimestamps();
    }

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'model');
    }
}
