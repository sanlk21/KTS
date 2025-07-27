<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'driver_id',
        'salary_record_id',
        'amount',
        'payment_date',
        'payment_type',
        'payment_method',
        'description',
        'reference_number'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    // Relationships
    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function salaryRecord()
    {
        return $this->belongsTo(DriverSalaryRecord::class, 'salary_record_id');
    }

    // Accessors
    public function getPaymentTypeColorAttribute()
    {
        switch ($this->payment_type) {
            case 'salary':
                return 'green';
            case 'advance':
                return 'blue';
            case 'bonus':
                return 'purple';
            case 'deduction':
                return 'red';
            case 'adjustment':
                return 'orange';
            default:
                return 'gray';
        }
    }

    // Scopes
    public function scopeForDriver($query, $driverId)
    {
        return $query->where('driver_id', $driverId);
    }

    public function scopeForDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('payment_date', [$startDate, $endDate]);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('payment_type', $type);
    }
}
