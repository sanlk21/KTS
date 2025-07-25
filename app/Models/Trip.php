<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    use HasFactory;

    protected $fillable = [
        'tipper_number',
        'driver_id',
        'driver_name',
        'plant_id',
        'plant_name',
        'delivery_date',
        'delivery_time',
        'trip_amount',
        'paid_amount'
    ];

    protected $casts = [
        'delivery_date' => 'date',
        'delivery_time' => 'datetime:H:i:s',
        'trip_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2'
    ];

    // Relationships
    public function tipper()
    {
        return $this->belongsTo(Tipper::class, 'tipper_number', 'tipper_number');
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }

    public function plant()
    {
        return $this->belongsTo(Plant::class, 'plant_id');
    }

    // Accessors
    public function getBalanceAmountAttribute()
    {
        return $this->trip_amount - $this->paid_amount;
    }

    public function getFormattedDeliveryTimeAttribute()
    {
        return $this->delivery_time ? $this->delivery_time->format('H:i') : null;
    }

    public function getFormattedDeliveryDateAttribute()
    {
        return $this->delivery_date ? $this->delivery_date->format('Y-m-d') : null;
    }

    // Scopes
    public function scopeToday($query)
    {
        return $query->whereDate('delivery_date', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('delivery_date', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('delivery_date', now()->month)
                    ->whereYear('delivery_date', now()->year);
    }

    public function scopeByDriver($query, $driverId)
    {
        return $query->where('driver_id', $driverId);
    }

    public function scopeByPlant($query, $plantId)
    {
        return $query->where('plant_id', $plantId);
    }

    public function scopePending($query)
    {
        return $query->whereRaw('trip_amount > paid_amount');
    }

    public function scopeCompleted($query)
    {
        return $query->whereRaw('trip_amount = paid_amount');
    }
}
