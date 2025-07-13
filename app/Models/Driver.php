<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'tipper_number',
        'nic',
        'phone_number',
        'address',
        'photo'
    ];

    public function tipper()
    {
        return $this->belongsTo(Tipper::class, 'tipper_number');
    }

    public function trips()
    {
        return $this->hasMany(Trip::class, 'driver_id');
    }

    // Accessor for photo URL
    public function getPhotoUrlAttribute()
    {
        return $this->photo ? asset('storage/' . $this->photo) : null;
    }
}
