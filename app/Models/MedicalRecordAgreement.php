<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalRecordAgreement extends Model
{
    use HasFactory;

    protected $fillable = [
        'medical_record_id',
        'pet_id',
        'agreement_signed',
    ];

    protected $casts = [
        'agreement_signed' => 'boolean',
    ];

    public function medicalRecord(): BelongsTo
    {
        return $this->belongsTo(MedicalRecord::class);
    }

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class, 'pet_id', 'pet_id');
    }
}