<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class DriverSalary extends Model
{
    use HasFactory;

    protected $fillable = [
        'driver_id',
        'daily_salary',
        'weekly_salary',
        'monthly_salary',
        'salary_type',
        'advance_amount',
        'is_active'
    ];

    protected $casts = [
        'daily_salary' => 'decimal:2',
        'weekly_salary' => 'decimal:2',
        'monthly_salary' => 'decimal:2',
        'advance_amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function salaryRecords()
    {
        return $this->hasMany(DriverSalaryRecord::class, 'driver_id', 'driver_id');
    }

    // Helper methods
    public function getExpectedSalaryForDate($date)
    {
        switch ($this->salary_type) {
            case 'daily':
                return $this->daily_salary;
            case 'weekly':
                // If it's start of week, return weekly salary, else 0
                return Carbon::parse($date)->dayOfWeek === 1 ? $this->weekly_salary : 0;
            case 'monthly':
                // If it's first day of month, return monthly salary, else 0
                return Carbon::parse($date)->day === 1 ? $this->monthly_salary : 0;
            default:
                return $this->daily_salary;
        }
    }

    public function getCurrentBalance()
    {
        return $this->salaryRecords()
            ->orderBy('record_date', 'desc')
            ->first()?->balance_amount ?? 0;
    }
}
