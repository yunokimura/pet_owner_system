<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PetTrait extends Model
{
    use HasFactory;

    protected $fillable = [
        'adoption_id',
        'trait_id',
    ];

    /**
     * Get the pet that owns this trait.
     */
    public function adoptionPet(): BelongsTo
    {
        return $this->belongsTo(AdoptionPet::class, 'adoption_id');
    }

    /**
     * Get the trait.
     */
    public function trait(): BelongsTo
    {
        return $this->belongsTo(AdoptionTrait::class, 'trait_id');
    }
}