<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class AdoptionApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'email',
        'mobile_number',
        'alt_mobile_number',
        'blk_lot_ph',
        'street',
        'barangay',
        'birth_date',
        'occupation',
        'company',
        'social_media',
        'adopted_before',
        'status',
        'alternate_contact',
        'questionnaire',
        'valid_id_path',
        'zoom_interview',
        'zoom_date',
        'zoom_time',
        'zoom_time_ampm',
        'shelter_visit',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'zoom_date' => 'date',
        'zoom_time' => 'time',
        'alternate_contact' => 'array',
        'questionnaire' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function selectedPets(): BelongsToMany
    {
        return $this->belongsToMany(AdoptionPet::class, 'adoption_selected_pets', 'adoption_application_id', 'adoption_pet_id')
                    ->withTimestamps();
    }

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'model');
    }

    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }
}