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
            'data' => $zones,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:150|regex:/^[\pL\s\-]+$/u',
            'severity' => 'nullable|string|in:leve,moderada,grave,desastre_total',
            'description' => 'nullable|string|not_regex:/[\{\}\<\>]/',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'status' => 'nullable|string|in:activa,controlada,evacuada',
            'is_attended' => 'nullable|boolean',
            'audit_name' => 'required|string|max:150|regex:/^[\pL\s\-]+$/u',
            'audit_phone' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $zone = AffectedZone::create($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Zona afectada registrada exitosamente',
            'data' => $zone,
        ], 201);
    }

    public function show($id)
    {
        $zone = AffectedZone::with(['updates' => function ($query) {
            $query->orderBy('created_at', 'desc');
        }])->find($id);

        if (! $zone) {
            return response()->json(['success' => false, 'message' => 'Zona afectada no encontrada'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $zone,
        ]);
    }

    public function update(Request $request, $id)
    {
        $zone = AffectedZone::find($id);
        if (! $zone) {
            return response()->json(['success' => false, 'message' => 'Zona afectada no encontrada'], 404);
        }

        $user = auth('api')->user() ?: auth()->user();
        $hasAdminRole = $user && in_array($user->role, ['superadmin', 'admin', 'coordinator']);
        $hasValidToken = $request->input('secure_token') === $zone->secure_token;

        if (!$hasAdminRole && !$hasValidToken) {
            return response()->json(['success' => false, 'message' => 'No autorizado para editar esta zona. Debes ser administrador/coordinador o proveer el token de seguridad correcto.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:150|regex:/^[\pL\s\-]+$/u',
            'severity' => 'sometimes|required|string|in:leve,moderada,grave,desastre_total',
            'status' => 'sometimes|required|string|in:activa,controlada,evacuada',
            'description' => 'sometimes|required|string|not_regex:/[\{\}\<\>]/',
            'latitude' => 'sometimes|required|numeric',
            'longitude' => 'sometimes|required|numeric',
            'is_attended' => 'sometimes|required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $zone->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Zona afectada actualizada',
            'data' => $zone,
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $zone = AffectedZone::find($id);
        if (! $zone) {
            return response()->json(['success' => false, 'message' => 'Zona afectada no encontrada'], 404);
        }

        $user = auth('api')->user() ?: auth()->user();
        $hasAdminRole = $user && in_array($user->role, ['superadmin', 'admin', 'coordinator']);
        $hasValidToken = $request->input('secure_token') === $zone->secure_token;

        if (!$hasAdminRole && !$hasValidToken) {
            return response()->json(['success' => false, 'message' => 'No autorizado para eliminar esta zona. Debes ser administrador/coordinador o proveer el token de seguridad correcto.'], 403);
        }

        $zone->delete();

        return response()->json([
            'success' => true,
            'message' => 'Zona eliminada exitosamente',
        ]);
    }
}
