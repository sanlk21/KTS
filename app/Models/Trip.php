<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    use HasFactory;

    protected $fillable = ['tipper_number', 'driver_id', 'plant_id', 'delivery_date', 'trip_amount', 'paid_amount'];

    public function tipper()
    {
        return $this->belongsTo(Tipper::class, 'tipper_number');
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }

    public function plant()
    {
        return $this->belongsTo(Plant::class, 'plant_id');
    }
}
