<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class MedicalRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'owner_id',
        'type',
        'appointment_date',
        'status',
        'metadata',
    ];

    protected $casts = [
        'appointment_date' => 'date',
        'metadata' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(PetOwner::class, 'owner_id', 'owner_id');
    }

    public function pets(): BelongsToMany
    {
        return $this->belongsToMany(Pet::class, 'medical_record_pets', 'medical_record_id', 'pet_id')
                    ->withTimestamps();
    }

    public function agreements(): HasMany
    {
        return $this->hasMany(MedicalRecordAgreement::class);
    }

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'model');
    }

    public function isVaccination(): bool
    {
        return $this->type === 'vaccination';
    }

    public function isKapon(): bool
    {
        return $this->type === 'kapon';
    }
}