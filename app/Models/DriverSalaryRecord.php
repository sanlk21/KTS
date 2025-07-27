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
        // Recalculate paid amount from related payments
        $totalPaid = $this->payments()->sum('amount');
        $this->paid_amount = $totalPaid;

        // Calculate balance: previous_balance + earned_amount - paid_amount
        // Note: We use earned_amount (what driver actually earned from trips)
        // not expected_amount (what they should earn based on salary config)
        $this->balance_amount = $this->previous_balance + $this->earned_amount - $this->paid_amount;

        // Update payment status based on earned vs paid comparison
        if ($this->paid_amount == 0) {
            $this->payment_status = 'pending';
        } elseif ($this->paid_amount < $this->earned_amount) {
            $this->payment_status = 'partial';
        } elseif ($this->paid_amount >= $this->earned_amount) {
            // If paid more than or equal to earned, mark as paid
            $this->payment_status = 'paid';
            if ($this->paid_amount > $this->earned_amount) {
                // Optional: You can have an 'overpaid' status if needed
                $this->payment_status = 'overpaid';
            }
        }

        $this->save();

        // Update balance for subsequent records
        $this->updateSubsequentRecords();
    }

    // Update balance for all subsequent records for this driver
    private function updateSubsequentRecords()
    {
        $subsequentRecords = self::where('driver_id', $this->driver_id)
            ->where('record_date', '>', $this->record_date)
            ->orderBy('record_date')
            ->get();

        $currentBalance = $this->balance_amount;

        foreach ($subsequentRecords as $record) {
            $record->previous_balance = $currentBalance;
            $record->balance_amount = $record->previous_balance + $record->earned_amount - $record->paid_amount;
            $record->save();
            $currentBalance = $record->balance_amount;
        }
    }

    // Static method to recalculate all balances for a driver
    public static function recalculateDriverBalances($driverId)
    {
        $records = self::where('driver_id', $driverId)
            ->orderBy('record_date')
            ->get();

        $runningBalance = 0;

        foreach ($records as $record) {
            $record->previous_balance = $runningBalance;

            // Recalculate paid amount from payments
            $totalPaid = $record->payments()->sum('amount');
            $record->paid_amount = $totalPaid;

            // Calculate new balance
            $record->balance_amount = $record->previous_balance + $record->earned_amount - $record->paid_amount;

            // Update payment status
            if ($record->paid_amount == 0) {
                $record->payment_status = 'pending';
            } elseif ($record->paid_amount < $record->earned_amount) {
                $record->payment_status = 'partial';
            } else {
                $record->payment_status = 'paid';
            }

            $record->save();
            $runningBalance = $record->balance_amount;
        }
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

    // Boot method to handle model events
    protected static function boot()
    {
        parent::boot();

        // When a record is created or updated, ensure balances are correct
        static::saved(function ($record) {
            // Only auto-update if this isn't part of a bulk recalculation
            if (!$record->skip_balance_update) {
                $record->updateSubsequentRecords();
            }
        });
    }
}
