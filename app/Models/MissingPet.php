<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MissingPet extends Model
{
    use HasFactory;

    protected $primaryKey = 'missing_id';
    public $incrementing = true;

    protected $fillable = [
        'name',
        'species',
        'breed',
        'age',
        'weight',
        'color',
        'gender',
        'last_seen_at',
        'description',
        'location',
        'status',
        'photo_img',
    ];

    protected $casts = [
        'last_seen_at' => 'datetime',
        'weight' => 'decimal:2',
    ];
}