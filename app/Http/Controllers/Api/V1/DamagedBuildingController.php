<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\DamagedBuilding;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DamagedBuildingController extends Controller
{
    public function index(Request $request)
    {
        $query = DamagedBuilding::query();

        if ($request->filled('city')) {
            $query->where('city', 'ILIKE', '%' . $request->city . '%');
        }

        if ($request->filled('damage_level')) {
            $query->where('damage_level', $request->damage_level);
        }

        return response()->json([
            'success' => true,
            'data' => $query->get()
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'city' => 'nullable|string|max:150',
            'zone' => 'nullable|string|max:150',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'damage_level' => 'nullable|string|max:50',
            'status' => 'nullable|string|max:50',
            'general_source' => 'nullable|string|max:255',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $building = DamagedBuilding::create($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Estructura reportada exitosamente',
            'data' => $building
        ], 201);
    }

    public function show($id)
    {
        $building = DamagedBuilding::find($id);

        if (!$building) {
            return response()->json(['success' => false, 'message' => 'Estructura no encontrada'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $building
        ]);
    }

    public function update(Request $request, $id)
    {
        $building = DamagedBuilding::find($id);

        if (!$building) {
            return response()->json(['success' => false, 'message' => 'Estructura no encontrada'], 404);
        }

        if ($request->header('X-Secure-Token') !== $building->secure_token) {
            return response()->json(['success' => false, 'message' => 'Token de seguridad inválido'], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'city' => 'sometimes|nullable|string|max:150',
            'zone' => 'sometimes|nullable|string|max:150',
            'latitude' => 'sometimes|nullable|numeric',
            'longitude' => 'sometimes|nullable|numeric',
            'damage_level' => 'sometimes|nullable|string|max:50',
            'status' => 'sometimes|nullable|string|max:50',
            'general_source' => 'sometimes|nullable|string|max:255',
            'notes' => 'sometimes|nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $building->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Estructura actualizada',
            'data' => $building
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $building = DamagedBuilding::find($id);

        if (!$building) {
            return response()->json(['success' => false, 'message' => 'Estructura no encontrada'], 404);
        }

        if ($request->header('X-Secure-Token') !== $building->secure_token) {
            return response()->json(['success' => false, 'message' => 'Token de seguridad inválido'], 403);
        }

        $building->delete();

        return response()->json([
            'success' => true,
            'message' => 'Estructura eliminada'
        ]);
    }
}
