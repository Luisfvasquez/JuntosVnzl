<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\CollectionCenter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CollectionCenterController extends Controller
{
    public function index()
    {
        $centers = CollectionCenter::with('supplies')->get();
        return response()->json([
            'success' => true,
            'message' => 'Centros de acopio obtenidos',
            'data' => $centers
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'center_name' => 'required|string|max:150',
            'manager_name' => 'required|string|max:150',
            'dni' => ['required', 'string', 'regex:/(V|E|J)-\d{5,9}/'],
            'phone' => ['required', 'string', 'regex:/^\+58(412|414|424|416|426)\d{7}$/'],
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'address_description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $center = CollectionCenter::create($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Centro de acopio creado exitosamente',
            'data' => $center
        ], 201);
    }

    public function show($id)
    {
        $center = CollectionCenter::with('supplies')->find($id);
        if (!$center) {
            return response()->json(['success' => false, 'message' => 'Centro no encontrado'], 404);
        }
        return response()->json([
            'success' => true,
            'message' => 'Centro obtenido',
            'data' => $center
        ]);
    }

    public function update(Request $request, $id)
    {
        $center = CollectionCenter::find($id);
        if (!$center) {
            return response()->json(['success' => false, 'message' => 'Centro no encontrado'], 404);
        }

        if ($request->header('X-Secure-Token') !== $center->secure_token) {
            return response()->json(['success' => false, 'message' => 'Token de seguridad inválido'], 403);
        }

        $validator = Validator::make($request->all(), [
            'center_name' => 'sometimes|required|string|max:150',
            'manager_name' => 'sometimes|required|string|max:150',
            'latitude' => 'sometimes|required|numeric',
            'longitude' => 'sometimes|required|numeric',
            'address_description' => 'sometimes|required|string',
            'status' => 'sometimes|required|string|in:recibiendo_insumos,capacidad_maxima,distribuyendo,en_traslado,inactivo',
            'dni' => 'prohibited',
            'phone' => 'prohibited',
        ], [
            'dni.prohibited' => 'El DNI no puede ser modificado después de la creación (Inmutabilidad).',
            'phone.prohibited' => 'El teléfono no puede ser modificado (Inmutabilidad).'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $center->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Centro actualizado',
            'data' => $center
        ]);
    }

    public function destroy($id)
    {
        $center = CollectionCenter::find($id);
        if (!$center) {
            return response()->json(['success' => false, 'message' => 'Centro no encontrado'], 404);
        }

        if (request()->header('X-Secure-Token') !== $center->secure_token) {
            return response()->json(['success' => false, 'message' => 'Token de seguridad inválido'], 403);
        }

        $center->delete();

        return response()->json([
            'success' => true,
            'message' => 'Centro eliminado exitosamente'
        ]);
    }
}
