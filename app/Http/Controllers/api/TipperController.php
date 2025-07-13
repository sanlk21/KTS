<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tipper;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class TipperController extends Controller
{
    // Show all tippers
    public function index()
    {
        $tippers = Tipper::with('drivers')->get();
        return Inertia::render('Tippers/Index', [
            'tippers' => $tippers,
        ]);
    }

    // Show the create form
    public function create()
    {
        return Inertia::render('Tippers/Create');
    }

    // Store a new tipper
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipper_number' => 'required|string|unique:tippers,tipper_number',
            'size' => 'required|string',
            'license_expiry' => 'required|date',
        ]);

        Tipper::create($validated);

        return redirect()->route('tippers.index')->with('success', 'Tipper created successfully.');
    }

    // Show the edit form
    public function edit($tipper_number)
    {
        $tipper = Tipper::where('tipper_number', $tipper_number)->firstOrFail();
        return Inertia::render('Tippers/Edit', [
            'tipper' => $tipper,
        ]);
    }

    // Update an existing tipper
    public function update(Request $request, $tipper_number)
    {
        $tipper = Tipper::where('tipper_number', $tipper_number)->firstOrFail();

        $validated = $request->validate([
            'tipper_number' => 'required|string|unique:tippers,tipper_number,' . $tipper->tipper_number . ',tipper_number',
            'size' => 'required|in:2,4',
            'license_expiry' => 'nullable|date',
        ]);

        $tipper->update($validated);

        return redirect()->route('tippers.index')->with('success', 'Tipper updated successfully.');
    }

    // Delete a tipper
    public function destroy($tipper_number)
    {
        $tipper = Tipper::where('tipper_number', $tipper_number)->firstOrFail();
        $tipper->delete();

        return redirect()->route('tippers.index')->with('success', 'Tipper deleted successfully.');
    }

    // Show a single tipper
    public function show($tipper_number)
    {
        $tipper = Tipper::where('tipper_number', $tipper_number)->with('drivers')->firstOrFail();

        return Inertia::render('Tippers/Show', [
            'tipper' => $tipper,
        ]);
    }

    // Get tippers with licenses expiring within the next 7 days
    public function getExpiringLicenses()
    {
        $now = Carbon::now();
        $weekFromNow = $now->copy()->addDays(7);

        $tippers = Tipper::whereBetween('license_expiry', [$now, $weekFromNow])->get();

        return Inertia::render('Tippers/Expiring', [
            'tippers' => $tippers,
        ]);
    }
}
