<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tipper extends Model
{
    use HasFactory;

    protected $primaryKey = 'tipper_number';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['tipper_number', 'size', 'license_expiry'];

    public function drivers()
    {
        return $this->hasMany(Driver::class, 'tipper_number');
    }

    public function trips()
    {
        return $this->hasMany(Trip::class, 'tipper_number');
    }
}
