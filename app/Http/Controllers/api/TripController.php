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
            'trip_amount_per_trip' => 'required|numeric|min:0',
            'driver_salary_per_trip' => 'required|numeric|min:0',
            'total_trips' => 'required|integer|min:1|max:50', // Added validation for total trips
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

        // Calculate totals
        $totalTripAmount = $request->trip_amount_per_trip * $request->total_trips;
        $totalDriverSalary = $request->driver_salary_per_trip * $request->total_trips;

        try {
            DB::beginTransaction();

            // Create multiple trips
            for ($i = 1; $i <= $request->total_trips; $i++) {
                Trip::create([
                    'tipper_number' => $request->tipper_number,
                    'driver_id' => $request->driver_id,
                    'driver_name' => $driver->name,
                    'plant_id' => $request->plant_id,
                    'plant_name' => $plant->name,
                    'delivery_date' => $request->delivery_date,
                    'delivery_time' => $request->delivery_time,
                    'trip_amount' => $request->trip_amount_per_trip,
                    'paid_amount' => $request->driver_salary_per_trip,
                    'trip_number' => $i, // Added trip number for reference
                    'batch_id' => uniqid(), // Added batch ID to group related trips
                    'total_trips_in_batch' => $request->total_trips,
                    'total_batch_amount' => $totalTripAmount,
                    'total_batch_salary' => $totalDriverSalary,
                    'net_income_per_trip' => $request->trip_amount_per_trip - $request->driver_salary_per_trip,
                    'total_net_income' => $totalTripAmount - $totalDriverSalary,
                ]);
            }

            DB::commit();

            return redirect()->route('trips.index')->with('success',
                "Successfully created {$request->total_trips} trips. Total Amount: $" . number_format($totalTripAmount, 2) .
                ", Total Driver Salary: $" . number_format($totalDriverSalary, 2) .
                ", Your Net Income: $" . number_format($totalTripAmount - $totalDriverSalary, 2)
            );

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors(['error' => 'Failed to create trips: ' . $e->getMessage()])->withInput();
        }
    }

    public function show($id)
    {
        $trip = Trip::with(['tipper', 'driver', 'plant'])->findOrFail($id);

        // Get related trips in the same batch if batch_id exists
        $relatedTrips = [];
        if ($trip->batch_id) {
            $relatedTrips = Trip::with(['tipper', 'driver', 'plant'])
                ->where('batch_id', $trip->batch_id)
                ->where('id', '!=', $id)
                ->orderBy('trip_number')
                ->get();
        }

        return inertia('trips/show', [
            'trip' => $trip,
            'relatedTrips' => $relatedTrips
        ]);
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
            'net_income_per_trip' => $request->trip_amount - $request->paid_amount,
        ]);

        return redirect()->route('trips.index')->with('success', 'Trip updated successfully');
    }

    public function destroy($id)
    {
        Trip::findOrFail($id)->delete();
        return redirect()->route('trips.index')->with('success', 'Trip deleted successfully');
    }

    // New method to delete entire batch
    public function destroyBatch($batchId)
    {
        try {
            $tripsCount = Trip::where('batch_id', $batchId)->count();
            Trip::where('batch_id', $batchId)->delete();

            return redirect()->route('trips.index')->with('success', "Successfully deleted {$tripsCount} trips from batch");
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to delete batch: ' . $e->getMessage()]);
        }
    }

    public function reports(Request $request)
    {
        $period = $request->query('period', 'monthly');
        $plantId = $request->query('plant_id');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        // Determine date range based on period
        $dateRange = $this->getDateRange($period, $startDate, $endDate);

        // Create base query - FIXED: Don't apply plant filter here yet
        $baseQuery = Trip::with(['driver', 'plant'])
            ->whereBetween('delivery_date', [$dateRange['start'], $dateRange['end']]);

        // Create separate queries for different statistics
        $summaryQuery = clone $baseQuery;
        $driverQuery = clone $baseQuery;
        $plantQuery = clone $baseQuery;
        $trendQuery = clone $baseQuery;
        $dailyQuery = clone $baseQuery;

        // Apply plant filter only if specific plant is selected
        if ($plantId && $plantId !== '') {
            $summaryQuery->where('plant_id', $plantId);
            $driverQuery->where('plant_id', $plantId);
            $plantQuery->where('plant_id', $plantId);
            $trendQuery->where('plant_id', $plantId);
            $dailyQuery->where('plant_id', $plantId);
        }

        // Get statistics
        $summary = $this->getSummaryStats($summaryQuery);
        $driverStats = $this->getDriverStats($driverQuery);
        $plantStats = $this->getPlantStats($plantQuery);
        $revenueTrend = $this->getRevenueTrend($trendQuery, $period);
        $dailyIncome = $this->getDailyIncomeBreakdown($dailyQuery, $period);
        $batchStats = $this->getBatchStats($baseQuery); // New batch statistics

        $reportData = [
            'summary' => $summary,
            'driver_stats' => $driverStats,
            'plant_stats' => $plantStats,
            'revenue_trend' => $revenueTrend,
            'daily_income' => $dailyIncome,
            'batch_stats' => $batchStats,
            'period' => $period,
            'date_range' => $dateRange,
            'selected_plant_id' => $plantId
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

    // New method for batch statistics
    private function getBatchStats($query)
    {
        return $query->select([
            'batch_id',
            'delivery_date',
            'driver_name',
            'plant_name',
            DB::raw('COUNT(*) as trips_in_batch'),
            DB::raw('SUM(trip_amount) as batch_revenue'),
            DB::raw('SUM(paid_amount) as batch_expenses'),
            DB::raw('SUM(trip_amount - paid_amount) as batch_profit'),
            DB::raw('AVG(trip_amount) as avg_trip_amount'),
        ])
        ->whereNotNull('batch_id')
        ->groupBy('batch_id', 'delivery_date', 'driver_name', 'plant_name')
        ->orderByDesc('delivery_date')
        ->limit(20)
        ->get()
        ->map(function ($batch) {
            return [
                'batch_id' => $batch->batch_id,
                'delivery_date' => $batch->delivery_date,
                'driver_name' => $batch->driver_name,
                'plant_name' => $batch->plant_name,
                'trips_in_batch' => (int) $batch->trips_in_batch,
                'batch_revenue' => (float) $batch->batch_revenue,
                'batch_expenses' => (float) $batch->batch_expenses,
                'batch_profit' => (float) $batch->batch_profit,
                'avg_trip_amount' => (float) $batch->avg_trip_amount,
                'profit_margin' => $batch->batch_revenue > 0 ?
                    round(($batch->batch_profit / $batch->batch_revenue) * 100, 2) : 0
            ];
        });
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

    private function getSummaryStats($query)
    {
        $results = $query->selectRaw('
            COUNT(*) as total_trips,
            SUM(trip_amount) as total_revenue,
            SUM(paid_amount) as total_expenses,
            AVG(trip_amount) as avg_trip_amount,
            AVG(paid_amount) as avg_driver_salary,
            COUNT(DISTINCT batch_id) as total_batches
        ')->first();

        return [
            'total_trips' => (int) $results->total_trips,
            'total_batches' => (int) $results->total_batches,
            'total_revenue' => (float) $results->total_revenue,
            'total_expenses' => (float) $results->total_expenses,
            'net_income' => (float) ($results->total_revenue - $results->total_expenses),
            'avg_trip_amount' => (float) $results->avg_trip_amount,
            'avg_driver_salary' => (float) $results->avg_driver_salary,
            'profit_margin' => $results->total_revenue > 0 ?
                round((($results->total_revenue - $results->total_expenses) / $results->total_revenue) * 100, 2) : 0
        ];
    }

    private function getDriverStats($query)
    {
        // Check what columns exist in the drivers table
        $driverColumns = DB::getSchemaBuilder()->getColumnListing('drivers');
        $hasPhone = in_array('phone', $driverColumns);
        $hasContact = in_array('contact', $driverColumns);
        $hasMobile = in_array('mobile', $driverColumns);

        // Build select array based on available columns
        $selectColumns = [
            'drivers.id',
            'drivers.name',
            DB::raw('COUNT(*) as total_trips'),
            DB::raw('COUNT(DISTINCT batch_id) as total_batches'),
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
        } else {
            // Add a default phone column if none exists
            $selectColumns[] = DB::raw("'N/A' as phone");
        }

        return $query->select($selectColumns)
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
                    'total_batches' => (int) $driver->total_batches,
                    'total_salary' => (float) $driver->total_salary,
                    'avg_per_trip' => (float) $driver->avg_per_trip,
                    'total_income_generated' => (float) $driver->total_income_generated,
                    'efficiency_score' => $driver->total_trips > 0 ?
                        round($driver->total_income_generated / $driver->total_trips, 2) : 0
                ];
            });
    }

    private function getPlantStats($query)
    {
        return $query->select([
            'plants.id',
            'plants.name',
            DB::raw('COUNT(*) as total_trips'),
            DB::raw('COUNT(DISTINCT batch_id) as total_batches'),
            DB::raw('SUM(trip_amount) as total_amount'),
            DB::raw('AVG(trip_amount) as avg_per_trip'),
            DB::raw('SUM(paid_amount) as total_driver_costs'),
            DB::raw('SUM(trip_amount - paid_amount) as total_profit')
        ])
        ->join('plants', 'trips.plant_id', '=', 'plants.id')
        ->groupBy('plants.id', 'plants.name')
        ->orderByDesc('total_amount')
        ->get()
        ->map(function ($plant) {
            return [
                'id' => $plant->id,
                'name' => $plant->name,
                'total_trips' => (int) $plant->total_trips,
                'total_batches' => (int) $plant->total_batches,
                'total_amount' => (float) $plant->total_amount,
                'avg_per_trip' => (float) $plant->avg_per_trip,
                'total_driver_costs' => (float) $plant->total_driver_costs,
                'total_profit' => (float) $plant->total_profit,
                'profit_margin' => $plant->total_amount > 0 ?
                    round(($plant->total_profit / $plant->total_amount) * 100, 2) : 0
            ];
        });
    }

    private function getRevenueTrend($query, $period)
    {
        $format = $this->getDateFormat($period);

        $data = $query->selectRaw("
            DATE_FORMAT(delivery_date, '{$format}') as date_label,
            SUM(trip_amount) as revenue,
            SUM(paid_amount) as expenses,
            SUM(trip_amount - paid_amount) as profit,
            COUNT(*) as trips,
            COUNT(DISTINCT batch_id) as batches
        ")
        ->groupBy('date_label')
        ->orderBy('delivery_date')
        ->get();

        return [
            'labels' => $data->pluck('date_label')->toArray(),
            'revenue' => $data->pluck('revenue')->toArray(),
            'expenses' => $data->pluck('expenses')->toArray(),
            'profit' => $data->pluck('profit')->toArray(),
            'trips' => $data->pluck('trips')->toArray(),
            'batches' => $data->pluck('batches')->toArray()
        ];
    }

    private function getDailyIncomeBreakdown($query, $period)
    {
        $data = $query->selectRaw('
            DATE(delivery_date) as date,
            SUM(trip_amount - paid_amount) as daily_income,
            COUNT(*) as daily_trips,
            COUNT(DISTINCT batch_id) as daily_batches
        ')
        ->groupBy('date')
        ->orderBy('date')
        ->get();

        return [
            'labels' => $data->pluck('date')->map(function ($date) {
                return Carbon::parse($date)->format('M d');
            })->toArray(),
            'data' => $data->pluck('daily_income')->toArray(),
            'trips' => $data->pluck('daily_trips')->toArray(),
            'batches' => $data->pluck('daily_batches')->toArray()
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
