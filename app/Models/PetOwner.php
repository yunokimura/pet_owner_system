<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PetOwner extends Model
{
    use HasFactory;

    protected $primaryKey = 'owner_id';

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'middle_name',
        'suffix',
        'phone_number',
        'house_no',
        'street',
        'subdivision',
        'barangay',
        'city',
        'province',
        'date_of_birth',
    ];

    /**
     * Get the user that owns the pet owner profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the pets for the pet owner.
     */
    public function pets()
    {
        return $this->hasMany(Pet::class, 'owner_id', 'owner_id');
    }
}
