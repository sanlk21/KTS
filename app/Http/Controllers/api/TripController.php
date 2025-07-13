<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Trip;
use App\Models\Tipper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class TripController extends Controller
{
    public function index()
    {
        return response()->json(Trip::with(['tipper', 'driver', 'plant'])->get());
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tipper_number' => 'required|string|exists:tippers,tipper_number',
            'driver_id' => 'required|integer|exists:drivers,id',
            'plant_id' => 'required|integer|exists:plants,id',
            'delivery_date' => 'required|date',
            'trip_amount' => 'required|numeric|min:0',
            'paid_amount' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Validate driver-tipper connection
        $driver = \App\Models\Driver::find($request->driver_id);
        if ($driver->tipper_number !== $request->tipper_number) {
            return response()->json(['error' => 'Driver is not assigned to this tipper'], 422);
        }

        $trip = Trip::create($request->all());
        return response()->json($trip, 201);
    }

    public function update(Request $request, $id)
    {
        $trip = Trip::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'tipper_number' => 'required|string|exists:tippers,tipper_number',
            'driver_id' => 'required|integer|exists:drivers,id',
            'plant_id' => 'required|integer|exists:plants,id',
            'delivery_date' => 'required|date',
            'trip_amount' => 'required|numeric|min:0',
            'paid_amount' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Validate driver-tipper connection
        $driver = \App\Models\Driver::find($request->driver_id);
        if ($driver->tipper_number !== $request->tipper_number) {
            return response()->json(['error' => 'Driver is not assigned to this tipper'], 422);
        }

        $trip->update($request->all());
        return response()->json($trip);
    }

    public function destroy($id)
    {
        Trip::findOrFail($id)->delete();
        return response()->json(null, 204);
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

        return response()->json([
            'deliveries_by_plant' => $deliveriesByPlant,
            'driver_salaries' => $salaries,
            'income' => $income,
        ]);
    }
}
