<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Plant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PlantController extends Controller
{
    public function index()
    {
        return response()->json(Plant::all());
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:plants',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $plant = Plant::create($request->all());
        return response()->json($plant, 201);
    }

    public function update(Request $request, $id)
    {
        $plant = Plant::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:plants,name,' . $id,
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $plant->update($request->all());
        return response()->json($plant);
    }

    public function destroy($id)
    {
        Plant::findOrFail($id)->delete();
        return response()->json(null, 204);
    }
}
