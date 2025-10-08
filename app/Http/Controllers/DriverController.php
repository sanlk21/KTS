<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\Tipper;
use App\Models\DriverAdvance;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class DriverController extends Controller
{
    public function index()
    {
        $drivers = Driver::with('tipper')->get()->map(function ($driver) {
            $data = [
                'id' => $driver->id,
                'name' => $driver->name,
                'tipper_number' => $driver->tipper_number,
                'nic' => $driver->nic,
                'phone_number' => $driver->phone_number,
                'address' => $driver->address,
                'photo_url' => $driver->photo_url,
                'tipper' => $driver->tipper
            ];
            \Log::info('Driver data: ', $data);
            return $data;
        });

        return Inertia::render('drivers/index', [
            'drivers' => $drivers
        ]);
    }

    public function create()
    {
        $tippers = Tipper::all(['tipper_number']);
        return Inertia::render('drivers/create', [
            'tippers' => $tippers
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'tipper_number' => 'required|string|exists:tippers,tipper_number',
            'nic' => 'required|string|max:20|unique:drivers,nic',
            'phone_number' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:5000',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $request->only(['name', 'tipper_number', 'nic', 'phone_number', 'address']);

        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('drivers', 'public');
            $data['photo'] = $photoPath;
        }

        Driver::create($data);
        return redirect()->route('drivers.index')->with('success', 'Driver created successfully.');
    }

    public function show(Driver $driver)
    {
        $driver->load('tipper');
        $driver->photo_url = $driver->photo_url;

        return Inertia::render('drivers/show', [
            'driver' => $driver
        ]);
    }

    public function edit(Driver $driver)
    {
        $tippers = Tipper::all(['tipper_number']);
        $driver->photo_url = $driver->photo_url;

        return Inertia::render('drivers/edit', [
            'driver' => $driver,
            'tippers' => $tippers
        ]);
    }

    public function update(Request $request, Driver $driver)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'tipper_number' => 'required|string|exists:tippers,tipper_number',
            'nic' => 'required|string|max:20|unique:drivers,nic,' . $driver->id,
            'phone_number' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $request->only(['name', 'tipper_number', 'nic', 'phone_number', 'address']);

        if ($request->hasFile('photo')) {
            if ($driver->photo && Storage::disk('public')->exists($driver->photo)) {
                Storage::disk('public')->delete($driver->photo);
            }

            $photoPath = $request->file('photo')->store('drivers', 'public');
            $data['photo'] = $photoPath;
        }

        $driver->update($data);
        return redirect()->route('drivers.index')->with('success', 'Driver updated successfully.');
    }

    public function destroy(Driver $driver)
    {
        if ($driver->photo && Storage::disk('public')->exists($driver->photo)) {
            Storage::disk('public')->delete($driver->photo);
        }

        $driver->delete();
        return redirect()->route('drivers.index')->with('success', 'Driver deleted successfully.');
    }

    /**
     * Get driver balance via API
     */
    public function getBalance($driverId)
    {
        try {
            $balance = DriverAdvance::getCurrentBalance($driverId);
            
            return response()->json([
                'success' => true,
                'balance' => $balance,
                'formatted' => 'Rs ' . number_format(abs($balance), 2),
                'status' => $balance < 0 ? 'owes' : 'positive'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve balance',
                'balance' => 0
            ], 500);
        }
    }
}