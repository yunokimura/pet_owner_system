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
        'breed',
        'age',
        'gender',
        'description',
        'traits',
        'image',
    ];
}