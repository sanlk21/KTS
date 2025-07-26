<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Models\Tipper;
use App\Models\Driver;
use App\Models\Plant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->query('period', 'monthly');

        // Get summary counts
        $summary = $this->getSummaryData();

        // Get expiring tippers (within 30 days)
        $expiringTippers = $this->getExpiringTippers();

        // Get reports data based on period
        $reports = $this->getReportsData($period);

        $dashboardData = [
            'summary' => $summary,
            'expiring_tippers' => $expiringTippers,
            'reports' => $reports,
            'period' => $period
        ];

        return inertia('Dashboard', [
            'dashboardData' => $dashboardData
        ]);
    }

    private function getSummaryData()
    {
        return [
            'tippers' => Tipper::count(),
            'drivers' => Driver::count(),
            'plants' => Plant::count(),
            'trips' => Trip::count()
        ];
    }

    private function getExpiringTippers()
    {
        $thirtyDaysFromNow = Carbon::now()->addDays(30);

        return Tipper::whereNotNull('license_expiry')
            ->where('license_expiry', '<=', $thirtyDaysFromNow)
            ->where('license_expiry', '>=', Carbon::now())
            ->orderBy('license_expiry')
            ->get(['tipper_number', 'license_expiry']);
    }

    private function getReportsData($period)
    {
        $dateRange = $this->getDateRange($period);

        // Get deliveries by plant
        $deliveriesByPlant = $this->getDeliveriesByPlant($dateRange);

        // Get driver salaries
        $driverSalaries = $this->getDriverSalaries($dateRange);

        // Get financial summary
        $income = $this->getIncomeData($dateRange);

        return [
            'deliveries_by_plant' => $deliveriesByPlant,
            'driver_salaries' => $driverSalaries,
            'income' => $income
        ];
    }

    private function getDateRange($period)
    {
        $now = Carbon::now();

        switch ($period) {
            case 'daily':
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
            default:
                return [
                    'start' => $now->copy()->startOfMonth(),
                    'end' => $now->copy()->endOfMonth()
                ];
        }
    }

    private function getDeliveriesByPlant($dateRange)
    {
        return Trip::select([
                'plants.name',
                DB::raw('COUNT(*) as trip_count')
            ])
            ->join('plants', 'trips.plant_id', '=', 'plants.id')
            ->whereBetween('delivery_date', [$dateRange['start'], $dateRange['end']])
            ->groupBy('plants.id', 'plants.name')
            ->orderByDesc('trip_count')
            ->limit(10) // Limit to top 10 plants for better visualization
            ->get()
            ->map(function ($plant) {
                return [
                    'name' => $plant->name,
                    'trip_count' => (int) $plant->trip_count
                ];
            });
    }

    private function getDriverSalaries($dateRange)
    {
        // Check what columns exist in the drivers table
        $driverColumns = DB::getSchemaBuilder()->getColumnListing('drivers');
        $hasPhone = in_array('phone', $driverColumns);
        $hasContact = in_array('contact', $driverColumns);
        $hasMobile = in_array('mobile', $driverColumns);

        $selectColumns = [
            'drivers.id',
            'drivers.name',
            DB::raw('SUM(paid_amount) as salary'),
            DB::raw('COUNT(*) as trip_count')
        ];

        $groupByColumns = ['drivers.id', 'drivers.name'];

        return Trip::select($selectColumns)
            ->join('drivers', 'trips.driver_id', '=', 'drivers.id')
            ->whereBetween('delivery_date', [$dateRange['start'], $dateRange['end']])
            ->groupBy($groupByColumns)
            ->orderByDesc('salary')
            ->limit(10) // Limit to top 10 drivers for better visualization
            ->get()
            ->map(function ($driver) {
                return [
                    'name' => $driver->name,
                    'salary' => (float) $driver->salary,
                    'trip_count' => (int) $driver->trip_count
                ];
            });
    }

    private function getIncomeData($dateRange)
    {
        $results = Trip::whereBetween('delivery_date', [$dateRange['start'], $dateRange['end']])
            ->selectRaw('
                SUM(trip_amount) as total_revenue,
                SUM(paid_amount) as total_expenses,
                SUM(trip_amount - paid_amount) as net_profit,
                COUNT(*) as total_trips
            ')
            ->first();

        // Calculate pending payments (this is a simplified calculation)
        // You might want to implement a more sophisticated pending payment system
        $pendingPayments = Trip::whereBetween('delivery_date', [$dateRange['start'], $dateRange['end']])
            ->where('paid_amount', '<', DB::raw('trip_amount'))
            ->sum(DB::raw('trip_amount - paid_amount'));

        return [
            'total_revenue' => (float) ($results->total_revenue ?? 0),
            'total_expenses' => (float) ($results->total_expenses ?? 0),
            'net_profit' => (float) ($results->net_profit ?? 0),
            'pending' => (float) ($pendingPayments ?? 0),
            'total_trips' => (int) ($results->total_trips ?? 0)
        ];
    }
}
