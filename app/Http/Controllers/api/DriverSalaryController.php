<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\DriverSalary;
use App\Models\DriverSalaryRecord;
use App\Models\DriverPayment;
use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DriverSalaryController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->query('period', 'daily');
        $driverId = $request->query('driver_id');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        // Get date range
        $dateRange = $this->getDateRange($period, $startDate, $endDate);

        // Build query
        $query = DriverSalaryRecord::with(['driver', 'driverSalary'])
            ->whereBetween('record_date', [$dateRange['start'], $dateRange['end']]);

        if ($driverId) {
            $query->where('driver_id', $driverId);
        }

        $salaryRecords = $query->orderBy('record_date', 'desc')
            ->orderBy('driver_id')
            ->paginate(20);

        // Get summary stats
        $summaryStats = $this->getSummaryStats($dateRange, $driverId);

        // Get drivers for filter
        $drivers = Driver::whereHas('driverSalary')->get();

        return inertia('driver-salaries/index', [
            'salaryRecords' => $salaryRecords,
            'summaryStats' => $summaryStats,
            'drivers' => $drivers,
            'filters' => [
                'period' => $period,
                'driver_id' => $driverId,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'date_range' => $dateRange
            ]
        ]);
    }

    public function create()
    {
        $drivers = Driver::all();

        return inertia('driver-salaries/create', [
            'drivers' => $drivers
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'driver_id' => 'required|exists:drivers,id',
            'daily_salary' => 'required|numeric|min:0',
            'weekly_salary' => 'nullable|numeric|min:0',
            'monthly_salary' => 'nullable|numeric|min:0',
            'salary_type' => 'required|in:daily,weekly,monthly',
            'advance_amount' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Check if driver already has salary configuration
        $existingSalary = DriverSalary::where('driver_id', $request->driver_id)->first();
        if ($existingSalary) {
            return redirect()->back()->withErrors(['driver_id' => 'Salary configuration already exists for this driver'])->withInput();
        }

        DriverSalary::create([
            'driver_id' => $request->driver_id,
            'daily_salary' => $request->daily_salary,
            'weekly_salary' => $request->weekly_salary ?? 0,
            'monthly_salary' => $request->monthly_salary ?? 0,
            'salary_type' => $request->salary_type,
            'advance_amount' => $request->advance_amount ?? 0,
        ]);

        return redirect()->route('driver-salaries.index')
            ->with('success', 'Driver salary configuration created successfully');
    }

    public function show($id)
    {
        $driver = Driver::with('driverSalary')->findOrFail($id);

        // Get salary records for last 30 days
        $salaryRecords = DriverSalaryRecord::where('driver_id', $id)
            ->with('payments')
            ->orderBy('record_date', 'desc')
            ->limit(30)
            ->get();

        // Get recent payments
        $recentPayments = DriverPayment::where('driver_id', $id)
            ->orderBy('payment_date', 'desc')
            ->limit(10)
            ->get();

        // Calculate stats
        $stats = $this->getDriverStats($id);

        return inertia('driver-salaries/show', [
            'driver' => $driver,
            'salaryRecords' => $salaryRecords,
            'recentPayments' => $recentPayments,
            'stats' => $stats
        ]);
    }

    public function edit($id)
    {
        $driverSalary = DriverSalary::with('driver')->findOrFail($id);

        return inertia('driver-salaries/edit', [
            'driverSalary' => $driverSalary
        ]);
    }

    public function update(Request $request, $id)
    {
        $driverSalary = DriverSalary::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'daily_salary' => 'required|numeric|min:0',
            'weekly_salary' => 'nullable|numeric|min:0',
            'monthly_salary' => 'nullable|numeric|min:0',
            'salary_type' => 'required|in:daily,weekly,monthly',
            'advance_amount' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $driverSalary->update([
            'daily_salary' => $request->daily_salary,
            'weekly_salary' => $request->weekly_salary ?? 0,
            'monthly_salary' => $request->monthly_salary ?? 0,
            'salary_type' => $request->salary_type,
            'advance_amount' => $request->advance_amount ?? 0,
            'is_active' => $request->is_active ?? true,
        ]);

        return redirect()->route('driver-salaries.index')
            ->with('success', 'Driver salary configuration updated successfully');
    }

    public function destroy($id)
    {
        $driverSalary = DriverSalary::findOrFail($id);

        // Check if there are any salary records
        $hasRecords = DriverSalaryRecord::where('driver_id', $driverSalary->driver_id)->exists();

        if ($hasRecords) {
            return redirect()->back()->withErrors(['error' => 'Cannot delete. Salary records exist for this driver.']);
        }

        $driverSalary->delete();

        return redirect()->route('driver-salaries.index')
            ->with('success', 'Driver salary configuration deleted successfully');
    }

    // Sync salary records from trips
    public function syncSalaryRecords(Request $request)
    {
        $date = $request->query('date', Carbon::today()->toDateString());
        $driverId = $request->query('driver_id');

        try {
            DB::beginTransaction();

            $driversQuery = Driver::whereHas('driverSalary');

            if ($driverId) {
                $driversQuery->where('id', $driverId);
            }

            $drivers = $driversQuery->get();
            $syncedCount = 0;

            foreach ($drivers as $driver) {
                if ($this->syncDriverSalaryForDate($driver, $date)) {
                    $syncedCount++;
                }
            }

            DB::commit();

            return redirect()->back()->with('success', "Synced salary records for {$syncedCount} drivers for date: {$date}");

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors(['error' => 'Failed to sync salary records: ' . $e->getMessage()]);
        }
    }

    // Make payment to driver
    public function makePayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'driver_id' => 'required|exists:drivers,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'payment_type' => 'required|in:salary,advance,bonus,deduction,adjustment',
            'payment_method' => 'required|string',
            'description' => 'nullable|string',
            'salary_record_id' => 'nullable|exists:driver_salary_records,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            // Create payment record
            $payment = DriverPayment::create([
                'driver_id' => $request->driver_id,
                'salary_record_id' => $request->salary_record_id,
                'amount' => $request->amount,
                'payment_date' => $request->payment_date,
                'payment_type' => $request->payment_type,
                'payment_method' => $request->payment_method,
                'description' => $request->description,
                'reference_number' => $request->reference_number,
            ]);

            // Update salary record if provided
            if ($request->salary_record_id) {
                $salaryRecord = DriverSalaryRecord::find($request->salary_record_id);
                if ($salaryRecord) {
                    $salaryRecord->paid_amount += $request->amount;
                    $salaryRecord->updateBalance();
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment recorded successfully',
                'payment' => $payment
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to record payment: ' . $e->getMessage()
            ], 500);
        }
    }

    // Private helper methods
    private function syncDriverSalaryForDate($driver, $date)
    {
        // Get trips for the driver on this date
        $tripsData = Trip::where('driver_id', $driver->id)
            ->whereDate('delivery_date', $date)
            ->selectRaw('
                SUM(paid_amount) as total_earned,
                COUNT(*) as total_trips
            ')
            ->first();

        $earnedAmount = $tripsData->total_earned ?? 0;
        $totalTrips = $tripsData->total_trips ?? 0;

        // Get expected salary for this date
        $expectedAmount = $driver->driverSalary->getExpectedSalaryForDate($date);

        // Get previous balance
        $previousRecord = DriverSalaryRecord::where('driver_id', $driver->id)
            ->where('record_date', '<', $date)
            ->orderBy('record_date', 'desc')
            ->first();

        $previousBalance = $previousRecord ? $previousRecord->balance_amount : 0;

        // Create or update salary record
        $salaryRecord = DriverSalaryRecord::updateOrCreate([
            'driver_id' => $driver->id,
            'record_date' => $date,
        ], [
            'earned_amount' => $earnedAmount,
            'expected_amount' => $expectedAmount,
            'previous_balance' => $previousBalance,
            'total_trips' => $totalTrips,
        ]);

        $salaryRecord->updateBalance();

        return true;
    }

    private function getDateRange($period, $startDate = null, $endDate = null)
    {
        $now = Carbon::now();

        switch ($period) {
            case 'today':
                return [
                    'start' => $now->copy()->startOfDay(),
                    'end' => $now->copy()->endOfDay()
                ];
            case 'weekly':
                return [
                    'start' => $now->copy()->startOfWeek(),
                    'end' => $now->copy()->endOfWeek()
                ];
            case 'monthly':
                return [
                    'start' => $now->copy()->startOfMonth(),
                    'end' => $now->copy()->endOfMonth()
                ];
            case 'custom':
                return [
                    'start' => Carbon::parse($startDate)->startOfDay(),
                    'end' => Carbon::parse($endDate)->endOfDay()
                ];
            default: // daily
                return [
                    'start' => $now->copy()->subDays(7)->startOfDay(),
                    'end' => $now->copy()->endOfDay()
                ];
        }
    }

    private function getSummaryStats($dateRange, $driverId = null)
    {
        $query = DriverSalaryRecord::whereBetween('record_date', [$dateRange['start'], $dateRange['end']]);

        if ($driverId) {
            $query->where('driver_id', $driverId);
        }

        $stats = $query->selectRaw('
            COUNT(*) as total_records,
            SUM(earned_amount) as total_earned,
            SUM(expected_amount) as total_expected,
            SUM(paid_amount) as total_paid,
            SUM(balance_amount) as total_balance,
            SUM(total_trips) as total_trips,
            COUNT(CASE WHEN payment_status = "pending" THEN 1 END) as pending_payments,
            COUNT(CASE WHEN balance_amount > 0 THEN 1 END) as drivers_owed,
            COUNT(CASE WHEN balance_amount < 0 THEN 1 END) as drivers_owing
        ')->first();

        return [
            'total_records' => (int) $stats->total_records,
            'total_earned' => (float) $stats->total_earned,
            'total_expected' => (float) $stats->total_expected,
            'total_paid' => (float) $stats->total_paid,
            'total_balance' => (float) $stats->total_balance,
            'total_trips' => (int) $stats->total_trips,
            'pending_payments' => (int) $stats->pending_payments,
            'drivers_owed' => (int) $stats->drivers_owed,
            'drivers_owing' => (int) $stats->drivers_owing,
            'outstanding_amount' => (float) $stats->total_balance,
        ];
    }

    private function getDriverStats($driverId)
    {
        $last30Days = Carbon::now()->subDays(30);

        $stats = DriverSalaryRecord::where('driver_id', $driverId)
            ->where('record_date', '>=', $last30Days)
            ->selectRaw('
                COUNT(*) as total_days,
                SUM(earned_amount) as total_earned,
                SUM(expected_amount) as total_expected,
                SUM(paid_amount) as total_paid,
                AVG(earned_amount) as avg_daily_earning,
                SUM(total_trips) as total_trips
            ')
            ->first();

        $currentBalance = DriverSalaryRecord::where('driver_id', $driverId)
            ->orderBy('record_date', 'desc')
            ->first()?->balance_amount ?? 0;

        return [
            'total_days' => (int) $stats->total_days,
            'total_earned' => (float) $stats->total_earned,
            'total_expected' => (float) $stats->total_expected,
            'total_paid' => (float) $stats->total_paid,
            'avg_daily_earning' => (float) $stats->avg_daily_earning,
            'total_trips' => (int) $stats->total_trips,
            'current_balance' => (float) $currentBalance,
        ];
    }
}
