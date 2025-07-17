<?php

namespace App\Http\Controllers;

use App\Models\Plant;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;

class PlantController extends Controller
{
    public function index()
    {
        $plants = Plant::all();
        return Inertia::render('plants/index', [
            'plants' => $plants,
        ])->with('flash',);
    }

    public function create()
    {
        return Inertia::render('plants/Store');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:plants',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        Plant::create($request->only(['name']));
        return redirect()->route('plants.index')->with('flash', ['success' => 'Plant created successfully']);
    }

    public function show($id)
    {
        $plant = Plant::findOrFail($id);
        return Inertia::render('plants/show', [
            'plant' => $plant,
        ]);
    }

    // ADDED EDIT METHOD
    public function edit($id)
    {
        $plant = Plant::findOrFail($id);
        return Inertia::render('plants/Edit', [
            'plant' => $plant,
        ]);
    }

    public function update(Request $request, $id)
    {
        $plant = Plant::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:plants,name,' . $id,
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $plant->update($request->only(['name']));
        return redirect()->route('plants.index')->with('flash', ['success' => 'Plant updated successfully']);
    }

    public function destroy($id)
    {
        Plant::findOrFail($id)->delete();
        return redirect()->route('plants.index')->with('flash', ['success' => 'Plant deleted successfully']);
    }
}
