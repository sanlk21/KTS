<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Trip;
use App\Models\Tipper;
use App\Models\Driver;
use App\Models\Plant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
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

        // Validate driver-tipper connection
        $driver = Driver::find($request->driver_id);
        if (!$driver) {
            return redirect()->back()->withErrors(['driver_id' => 'Driver not found'])->withInput();
        }

        if ($driver->tipper_number !== $request->tipper_number) {
            return redirect()->back()->withErrors(['error' => 'Driver is not assigned to this tipper'])->withInput();
        }

        // Get plant information
        $plant = Plant::find($request->plant_id);
        if (!$plant) {
            return redirect()->back()->withErrors(['plant_id' => 'Plant not found'])->withInput();
        }

        // Validate paid amount doesn't exceed trip amount
        if ($request->paid_amount > $request->trip_amount) {
            return redirect()->back()->withErrors(['paid_amount' => 'Paid amount cannot exceed trip amount'])->withInput();
        }

        Trip::create([
            'tipper_number' => $request->tipper_number,
            'driver_id' => $request->driver_id,
            'driver_name' => $driver->name, // Add driver name
            'plant_id' => $request->plant_id,
            'plant_name' => $plant->name, // Add plant name
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

        // Validate driver-tipper connection
        $driver = Driver::find($request->driver_id);
        if (!$driver) {
            return redirect()->back()->withErrors(['driver_id' => 'Driver not found'])->withInput();
        }

        if ($driver->tipper_number !== $request->tipper_number) {
            return redirect()->back()->withErrors(['error' => 'Driver is not assigned to this tipper'])->withInput();
        }

        // Get plant information
        $plant = Plant::find($request->plant_id);
        if (!$plant) {
            return redirect()->back()->withErrors(['plant_id' => 'Plant not found'])->withInput();
        }

        // Validate paid amount doesn't exceed trip amount
        if ($request->paid_amount > $request->trip_amount) {
            return redirect()->back()->withErrors(['paid_amount' => 'Paid amount cannot exceed trip amount'])->withInput();
        }

        $trip->update([
            'tipper_number' => $request->tipper_number,
            'driver_id' => $request->driver_id,
            'driver_name' => $driver->name, // Update driver name
            'plant_id' => $request->plant_id,
            'plant_name' => $plant->name, // Update plant name
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
        $period = $request->query('period', 'daily');
        $startDate = Carbon::now()->startOfDay();
        if ($period === 'weekly') {
            $startDate = Carbon::now()->startOfWeek();
        } elseif ($period === 'monthly') {
            $startDate = Carbon::now()->startOfMonth();
        }

        // Deliveries by plant
        $deliveriesByPlant = Trip::select('plants.name')
            ->join('plants', 'trips.plant_id', '=', 'plants.id')
            ->where('delivery_date', '>=', $startDate)
            ->groupBy('plant_id', 'plants.name')
            ->selectRaw('count(*) as trip_count')
            ->get();

        // Driver salaries
        $salaries = Trip::select('drivers.id', 'drivers.name', 'tippers.size')
            ->join('drivers', 'trips.driver_id', '=', 'drivers.id')
            ->join('tippers', 'trips.tipper_number', '=', 'tippers.tipper_number')
            ->where('delivery_date', '>=', $startDate)
            ->groupBy('drivers.id', 'drivers.name', 'tippers.size')
            ->selectRaw('count(*) * (case tippers.size when 2 then 800 when 4 then 1000 end) as salary')
            ->get();

        // Income and pending payments
        $income = Trip::where('delivery_date', '>=', $startDate)
            ->selectRaw('sum(trip_amount) as total_income, sum(trip_amount - paid_amount) as pending')
            ->first();

        return inertia('trips/reports', [
            'deliveries_by_plant' => $deliveriesByPlant,
            'driver_salaries' => $salaries,
            'income' => $income,
            'period' => $period
        ]);
    }
}
