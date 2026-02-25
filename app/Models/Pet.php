<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
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

    /**
     * Get the pet owner that owns the pet.
     */
    public function owner()
    {
        return $this->belongsTo(PetOwner::class, 'owner_id', 'owner_id');
    }
}
