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
        'paid_amount',
        'advance_amount',
        'actual_paid',
        'balance_due',
        'payment_notes',
        'trip_number',
        'batch_id',
        'total_trips_in_batch',
        'total_batch_amount',
        'total_batch_salary',
        'net_income_per_trip',
        'total_net_income'
    ];

    protected $casts = [
        'delivery_date' => 'date',
        'delivery_time' => 'datetime:H:i:s',
        'trip_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'advance_amount' => 'decimal:2',
        'actual_paid' => 'decimal:2',
        'balance_due' => 'decimal:2'
    ];

    protected $appends = ['driver_current_balance'];

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

    public function advances()
    {
        return $this->hasMany(DriverAdvance::class);
    }

    // Accessors
    public function getBalanceAmountAttribute()
    {
        return $this->trip_amount - $this->paid_amount;
    }

    public function getDriverCurrentBalanceAttribute()
    {
        if (!$this->driver_id) return 0;
        return DriverAdvance::getCurrentBalance($this->driver_id);
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

    // New scope for batch trips
    public function scopeByBatch($query, $batchId)
    {
        return $query->where('batch_id', $batchId);
    }

    // New scope for date range
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('delivery_date', [$startDate, $endDate]);
    }

    // New method to calculate total advance given in batch
    public function getTotalAdvanceInBatchAttribute()
    {
        if (!$this->batch_id) return 0;
        
        return self::where('batch_id', $this->batch_id)
            ->sum('advance_amount');
    }

    // New method to calculate total actual paid in batch
    public function getTotalActualPaidInBatchAttribute()
    {
        if (!$this->batch_id) return 0;
        
        return self::where('batch_id', $this->batch_id)
            ->sum('actual_paid');
    }
}