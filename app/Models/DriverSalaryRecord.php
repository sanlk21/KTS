<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class DriverSalaryRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'driver_id',
        'record_date',
        'earned_amount',
        'expected_amount',
        'paid_amount',
        'balance_amount',
        'previous_balance',
        'total_trips',
        'notes',
        'payment_status'
    ];

    protected $casts = [
        'record_date' => 'date',
        'earned_amount' => 'decimal:2',
        'expected_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'balance_amount' => 'decimal:2',
        'previous_balance' => 'decimal:2',
        'total_trips' => 'integer',
    ];

    // Relationships
    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function payments()
    {
        return $this->hasMany(DriverPayment::class, 'salary_record_id');
    }

    public function driverSalary()
    {
        return $this->belongsTo(DriverSalary::class, 'driver_id', 'driver_id');
    }

    // Accessors
    public function getStatusColorAttribute()
    {
        switch ($this->payment_status) {
            case 'paid':
                return 'green';
            case 'partial':
                return 'yellow';
            case 'overpaid':
                return 'blue';
            case 'pending':
            default:
                return 'red';
        }
    }

    public function getBalanceStatusAttribute()
    {
        if ($this->balance_amount > 0) {
            return 'owe_driver'; // We owe money to driver
        } elseif ($this->balance_amount < 0) {
            return 'driver_owes'; // Driver owes money to us
        } else {
            return 'balanced'; // All settled
        }
    }

    // Helper methods
    public function updateBalance()
    {
        // Calculate new balance: previous_balance + earned_amount - expected_amount + paid_amount
        $this->balance_amount = $this->previous_balance + $this->earned_amount - $this->expected_amount;

        // Update payment status
        if ($this->paid_amount == 0) {
            $this->payment_status = 'pending';
        } elseif ($this->paid_amount < $this->expected_amount) {
            $this->payment_status = 'partial';
        } elseif ($this->paid_amount == $this->expected_amount) {
            $this->payment_status = 'paid';
        } else {
            $this->payment_status = 'overpaid';
        }

        $this->save();
    }

    // Scopes
    public function scopeForDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('record_date', [$startDate, $endDate]);
    }

    public function scopeForDriver($query, $driverId)
    {
        return $query->where('driver_id', $driverId);
    }

    public function scopePending($query)
    {
        return $query->where('payment_status', 'pending');
    }

    public function scopeOweMoney($query)
    {
        return $query->where('balance_amount', '>', 0);
    }

    public function scopeDriverOwes($query)
    {
        return $query->where('balance_amount', '<', 0);
    }
}
