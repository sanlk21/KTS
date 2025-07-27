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
        'photo',
        'liaison_number',
    ];

    // Existing relationships
    public function tipper()
    {
        return $this->belongsTo(Tipper::class, 'tipper_number');
    }

    public function trips()
    {
        return $this->hasMany(Trip::class, 'driver_id');
    }

    // New relationships for driver salary management
    public function driverSalary()
    {
        return $this->hasOne(DriverSalary::class);
    }

    public function salaryRecords()
    {
        return $this->hasMany(DriverSalaryRecord::class);
    }

    public function payments()
    {
        return $this->hasMany(DriverPayment::class);
    }

    // Accessor for photo URL
    public function getPhotoUrlAttribute()
    {
        return $this->photo ? asset('storage/' . $this->photo) : null;
    }
}
