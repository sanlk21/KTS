<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Trip;
use App\Models\Tipper;
use App\Models\Driver;
use App\Models\Plant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TripController extends Controller
{
    public function index()
    {
        $trips = Trip::with(['tipper', 'driver', 'plant'])->latest()->paginate(10);
        return inertia('trips/index', ['trips' => $trips]);
    }

    public function create()
    {
        $drivers = Driver::all();
        $plants = Plant::all();
        $tippers = Tipper::all();

        return inertia('trips/create', [
            'drivers' => $drivers,
            'plants' => $plants,
            'tippers' => $tippers
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tipper_number' => 'required|string|exists:tippers,tipper_number',
            'driver_id' => 'required|integer|exists:drivers,id',
            'plant_id' => 'required|integer|exists:plants,id',
            'delivery_date' => 'required|date',
            'delivery_time' => 'nullable|date_format:H:i',
            'trip_amount' => 'required|numeric|min:0',
            'paid_amount' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Get driver and plant information
        $driver = Driver::find($request->driver_id);
        if (!$driver) {
            return redirect()->back()->withErrors(['driver_id' => 'Driver not found'])->withInput();
        }

        $plant = Plant::find($request->plant_id);
        if (!$plant) {
            return redirect()->back()->withErrors(['plant_id' => 'Plant not found'])->withInput();
        }

        // Note: Removed the validation that paid_amount cannot exceed trip_amount
        // since driver salary can be higher than the trip amount in some cases

        Trip::create([
            'tipper_number' => $request->tipper_number,
            'driver_id' => $request->driver_id,
            'driver_name' => $driver->name,
            'plant_id' => $request->plant_id,
            'plant_name' => $plant->name,
            'delivery_date' => $request->delivery_date,
            'delivery_time' => $request->delivery_time,
            'trip_amount' => $request->trip_amount,
            'paid_amount' => $request->paid_amount,
        ]);

        return redirect()->route('trips.index')->with('success', 'Trip created successfully');
    }

    public function show($id)
    {
        $trip = Trip::with(['tipper', 'driver', 'plant'])->findOrFail($id);
        return inertia('trips/show', ['trip' => $trip]);
    }

    public function edit($id)
    {
        $trip = Trip::findOrFail($id);
        $drivers = Driver::all();
        $plants = Plant::all();
        $tippers = Tipper::all();

        return inertia('trips/edit', [
            'trip' => $trip,
            'drivers' => $drivers,
            'plants' => $plants,
            'tippers' => $tippers
        ]);
    }

    public function update(Request $request, $id)
    {
        $trip = Trip::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'tipper_number' => 'required|string|exists:tippers,tipper_number',
            'driver_id' => 'required|integer|exists:drivers,id',
            'plant_id' => 'required|integer|exists:plants,id',
            'delivery_date' => 'required|date',
            'delivery_time' => 'nullable|date_format:H:i',
            'trip_amount' => 'required|numeric|min:0',
            'paid_amount' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Get driver and plant information
        $driver = Driver::find($request->driver_id);
        if (!$driver) {
            return redirect()->back()->withErrors(['driver_id' => 'Driver not found'])->withInput();
        }

        $plant = Plant::find($request->plant_id);
        if (!$plant) {
            return redirect()->back()->withErrors(['plant_id' => 'Plant not found'])->withInput();
        }

        $trip->update([
            'tipper_number' => $request->tipper_number,
            'driver_id' => $request->driver_id,
            'driver_name' => $driver->name,
            'plant_id' => $request->plant_id,
            'plant_name' => $plant->name,
            'delivery_date' => $request->delivery_date,
            'delivery_time' => $request->delivery_time,
            'trip_amount' => $request->trip_amount,
            'paid_amount' => $request->paid_amount,
        ]);

        return redirect()->route('trips.index')->with('success', 'Trip updated successfully');
    }

    public function destroy($id)
    {
        Trip::findOrFail($id)->delete();
        return redirect()->route('trips.index')->with('success', 'Trip deleted successfully');
    }

    public function reports(Request $request)
    {
        $period = $request->query('period', 'monthly');
        $plantId = $request->query('plant_id');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        // Determine date range based on period
        $dateRange = $this->getDateRange($period, $startDate, $endDate);

        $baseQuery = Trip::with(['driver', 'plant'])
            ->whereBetween('delivery_date', [$dateRange['start'], $dateRange['end']]);

        if ($plantId) {
            $baseQuery->where('plant_id', $plantId);
        }

        // Summary statistics
        $summary = $this->getSummaryStats($baseQuery);

        // Driver statistics
        $driverStats = $this->getDriverStats($baseQuery);

        // Plant statistics
        $plantStats = $this->getPlantStats($baseQuery);

        // Revenue trend data
        $revenueTrend = $this->getRevenueTrend($baseQuery, $period);

        // Daily income breakdown
        $dailyIncome = $this->getDailyIncomeBreakdown($baseQuery, $period);

        $reportData = [
            'summary' => $summary,
            'driver_stats' => $driverStats,
            'plant_stats' => $plantStats,
            'revenue_trend' => $revenueTrend,
            'daily_income' => $dailyIncome,
            'period' => $period,
            'date_range' => $dateRange
        ];

        // Handle export requests
        if ($request->query('export') === 'pdf') {
            return $this->exportToPDF($reportData);
        }

        $plants = Plant::all();

        return inertia('trips/reports', [
            'reportData' => $reportData,
            'plants' => $plants
        ]);
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
            case 'yesterday':
                return [
                    'start' => $now->copy()->subDay()->startOfDay(),
                    'end' => $now->copy()->subDay()->endOfDay()
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
            case 'quarterly':
                return [
                    'start' => $now->copy()->startOfQuarter(),
                    'end' => $now->copy()->endOfQuarter()
                ];
            case 'yearly':
                return [
                    'start' => $now->copy()->startOfYear(),
                    'end' => $now->copy()->endOfYear()
                ];
            case 'custom':
                return [
                    'start' => Carbon::parse($startDate)->startOfDay(),
                    'end' => Carbon::parse($endDate)->endOfDay()
                ];
            default:
                return [
                    'start' => $now->copy()->startOfMonth(),
                    'end' => $now->copy()->endOfMonth()
                ];
        }
    }

    private function getSummaryStats($baseQuery)
    {
        $results = $baseQuery->selectRaw('
            COUNT(*) as total_trips,
            SUM(trip_amount) as total_revenue,
            SUM(paid_amount) as total_expenses,
            AVG(trip_amount) as avg_trip_amount,
            AVG(paid_amount) as avg_driver_salary
        ')->first();

        return [
            'total_trips' => (int) $results->total_trips,
            'total_revenue' => (float) $results->total_revenue,
            'total_expenses' => (float) $results->total_expenses,
            'net_income' => (float) ($results->total_revenue - $results->total_expenses),
            'avg_trip_amount' => (float) $results->avg_trip_amount,
            'avg_driver_salary' => (float) $results->avg_driver_salary,
            'profit_margin' => $results->total_revenue > 0 ?
                round((($results->total_revenue - $results->total_expenses) / $results->total_revenue) * 100, 2) : 0
        ];
    }

    private function getDriverStats($baseQuery)
    {
        // First, let's check what columns exist in the drivers table
        $driverColumns = DB::getSchemaBuilder()->getColumnListing('drivers');
        $hasPhone = in_array('phone', $driverColumns);
        $hasContact = in_array('contact', $driverColumns);
        $hasMobile = in_array('mobile', $driverColumns);

        // Build select array based on available columns
        $selectColumns = [
            'drivers.id',
            'drivers.name',
            DB::raw('COUNT(*) as total_trips'),
            DB::raw('SUM(paid_amount) as total_salary'),
            DB::raw('AVG(paid_amount) as avg_per_trip'),
            DB::raw('SUM(trip_amount - paid_amount) as total_income_generated')
        ];

        $groupByColumns = ['drivers.id', 'drivers.name'];

        // Add phone/contact column if it exists
        if ($hasPhone) {
            $selectColumns[] = 'drivers.phone';
            $groupByColumns[] = 'drivers.phone';
        } elseif ($hasContact) {
            $selectColumns[] = 'drivers.contact as phone';
            $groupByColumns[] = 'drivers.contact';
        } elseif ($hasMobile) {
            $selectColumns[] = 'drivers.mobile as phone';
            $groupByColumns[] = 'drivers.mobile';
        }

        return $baseQuery->select($selectColumns)
        ->join('drivers', 'trips.driver_id', '=', 'drivers.id')
        ->groupBy($groupByColumns)
        ->orderByDesc('total_trips')
        ->get()
        ->map(function ($driver) {
            return [
                'id' => $driver->id,
                'name' => $driver->name,
                'phone' => $driver->phone ?? 'N/A',
                'total_trips' => (int) $driver->total_trips,
                'total_salary' => (float) $driver->total_salary,
                'avg_per_trip' => (float) $driver->avg_per_trip,
                'total_income_generated' => (float) $driver->total_income_generated,
                'efficiency_score' => $driver->total_trips > 0 ?
                    round($driver->total_income_generated / $driver->total_trips, 2) : 0
            ];
        });
    }

   private function getPlantStats($baseQuery)
{
    return $baseQuery->select([
        'plants.id',
        'plants.name',
        DB::raw('COUNT(*) as total_trips'),
        DB::raw('SUM(trip_amount) as total_amount'),
        DB::raw('AVG(trip_amount) as avg_per_trip'),
        DB::raw('SUM(paid_amount) as total_driver_costs'),
        DB::raw('SUM(trip_amount - paid_amount) as total_profit')
    ])
    ->join('plants', 'trips.plant_id', '=', 'plants.id')
    ->groupBy('plants.id', 'plants.name') // Removed plants.location
    ->orderByDesc('total_amount')
    ->get()
    ->map(function ($plant) {
        return [
            'id' => $plant->id,
            'name' => $plant->name,
            'location' => 'N/A', // Set location to 'N/A' or remove it
            'total_trips' => (int) $plant->total_trips,
            'total_amount' => (float) $plant->total_amount,
            'avg_per_trip' => (float) $plant->avg_per_trip,
            'total_driver_costs' => (float) $plant->total_driver_costs,
            'total_profit' => (float) $plant->total_profit,
            'profit_margin' => $plant->total_amount > 0 ?
                round(($plant->total_profit / $plant->total_amount) * 100, 2) : 0
        ];
    });
}

    private function getRevenueTrend($baseQuery, $period)
    {
        $format = $this->getDateFormat($period);

        $data = $baseQuery->selectRaw("
            DATE_FORMAT(delivery_date, '{$format}') as date_label,
            SUM(trip_amount) as revenue,
            SUM(paid_amount) as expenses,
            SUM(trip_amount - paid_amount) as profit,
            COUNT(*) as trips
        ")
        ->groupBy('date_label')
        ->orderBy('delivery_date')
        ->get();

        return [
            'labels' => $data->pluck('date_label')->toArray(),
            'revenue' => $data->pluck('revenue')->toArray(),
            'expenses' => $data->pluck('expenses')->toArray(),
            'profit' => $data->pluck('profit')->toArray(),
            'trips' => $data->pluck('trips')->toArray()
        ];
    }

    private function getDailyIncomeBreakdown($baseQuery, $period)
    {
        $data = $baseQuery->selectRaw('
            DATE(delivery_date) as date,
            SUM(trip_amount - paid_amount) as daily_income,
            COUNT(*) as daily_trips
        ')
        ->groupBy('date')
        ->orderBy('date')
        ->get();

        return [
            'labels' => $data->pluck('date')->map(function ($date) {
                return Carbon::parse($date)->format('M d');
            })->toArray(),
            'data' => $data->pluck('daily_income')->toArray(),
            'trips' => $data->pluck('daily_trips')->toArray()
        ];
    }

    private function getDateFormat($period)
    {
        switch ($period) {
            case 'today':
            case 'yesterday':
                return '%H:00'; // Hour format
            case 'weekly':
            case 'monthly':
                return '%Y-%m-%d'; // Daily format
            case 'quarterly':
            case 'yearly':
                return '%Y-%m'; // Monthly format
            default:
                return '%Y-%m-%d';
        }
    }

    private function exportToPDF($data)
    {
        // This would implement PDF export functionality
        // You could use libraries like DomPDF or wkhtmltopdf

        // For now, return JSON response indicating export functionality
        return response()->json([
            'message' => 'PDF export functionality would be implemented here',
            'data' => $data
        ]);
    }
}
