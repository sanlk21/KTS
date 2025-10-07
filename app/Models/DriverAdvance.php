<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverAdvance extends Model
{
    use HasFactory;

    protected $fillable = [
        'driver_id',
        'trip_id',
        'type',
        'amount',
        'balance_after',
        'notes',
        'transaction_date'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'transaction_date' => 'datetime'
    ];

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    // Get current balance for a driver
    public static function getCurrentBalance($driverId)
    {
        $latest = self::where('driver_id', $driverId)
            ->latest('transaction_date')
            ->latest('id')
            ->first();
            
        return $latest ? $latest->balance_after : 0;
    }

    // Record advance payment
    public static function recordAdvance($driverId, $amount, $tripId = null, $notes = null)
    {
        $currentBalance = self::getCurrentBalance($driverId);
        $newBalance = $currentBalance - $amount; // Negative means driver owes

        return self::create([
            'driver_id' => $driverId,
            'trip_id' => $tripId,
            'type' => 'advance',
            'amount' => $amount,
            'balance_after' => $newBalance,
            'notes' => $notes,
            'transaction_date' => now()
        ]);
    }

    // Record deduction from salary
    public static function recordDeduction($driverId, $amount, $tripId = null, $notes = null)
    {
        $currentBalance = self::getCurrentBalance($driverId);
        $newBalance = $currentBalance + $amount; // Adding back reduces debt

        return self::create([
            'driver_id' => $driverId,
            'trip_id' => $tripId,
            'type' => 'deduction',
            'amount' => $amount,
            'balance_after' => $newBalance,
            'notes' => $notes,
            'transaction_date' => now()
        ]);
    }

    // Record actual payment
    public static function recordPayment($driverId, $amount, $tripId = null, $notes = null)
    {
        $currentBalance = self::getCurrentBalance($driverId);
        $newBalance = $currentBalance + $amount;

        return self::create([
            'driver_id' => $driverId,
            'trip_id' => $tripId,
            'type' => 'payment',
            'amount' => $amount,
            'balance_after' => $newBalance,
            'notes' => $notes,
            'transaction_date' => now()
        ]);
    }
}