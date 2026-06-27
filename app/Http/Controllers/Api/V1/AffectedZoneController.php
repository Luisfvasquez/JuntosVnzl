<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\AffectedZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AffectedZoneController extends Controller
{
    public function index()
    {
        $zones = AffectedZone::all();
        return response()->json([
            'success' => true,
            'data' => $zones
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:150',
            'severity' => 'nullable|string|in:leve,moderada,grave,desastre_total',
            'description' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'status' => 'nullable|string|in:activa,controlada,evacuada',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $zone = AffectedZone::create($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Zona afectada registrada exitosamente',
            'data' => $zone
        ], 201);
    }

    public function show($id)
    {
        $zone = AffectedZone::find($id);
        if (!$zone) {
            return response()->json(['success' => false, 'message' => 'Zona afectada no encontrada'], 404);
        }
        return response()->json([
            'success' => true,
            'data' => $zone
        ]);
    }

    public function update(Request $request, $id)
    {
        $zone = AffectedZone::find($id);
        if (!$zone) {
            return response()->json(['success' => false, 'message' => 'Zona afectada no encontrada'], 404);
        }

        if ($request->header('X-Secure-Token') !== $zone->secure_token) {
            return response()->json(['success' => false, 'message' => 'Token de seguridad inválido'], 403);
        }

        $validator = Validator::make($request->all(), [
            'severity' => 'sometimes|required|string|in:leve,moderada,grave,desastre_total',
            'status' => 'sometimes|required|string|in:activa,controlada,evacuada',
            'description' => 'sometimes|required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $zone->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Zona afectada actualizada',
            'data' => $zone
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $zone = AffectedZone::find($id);
        if (!$zone) {
            return response()->json(['success' => false, 'message' => 'Zona afectada no encontrada'], 404);
        }

        if ($request->header('X-Secure-Token') !== $zone->secure_token) {
            return response()->json(['success' => false, 'message' => 'Token de seguridad inválido'], 403);
        }

        $zone->delete();

        return response()->json([
            'success' => true,
            'message' => 'Zona eliminada exitosamente'
        ]);
    }
}
