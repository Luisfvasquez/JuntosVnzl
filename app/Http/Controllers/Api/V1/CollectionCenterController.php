<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\CollectionCenter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CollectionCenterController extends Controller
{
    public function index(Request $request)
    {
        $query = CollectionCenter::with('supplies');

        if ($request->has('page') || $request->has('per_page')) {
            $centers = $query->paginate($request->query('per_page', 15));
        } else {
            $centers = $query->get();
        }

        return response()->json([
            'success' => true,
            'message' => 'Centros de acopio obtenidos',
            'data' => $centers
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'center_name' => 'required|string|max:150|regex:/^[\pL\s\-]+$/u',
            'manager_name' => 'required|string|max:150|regex:/^[\pL\s\-]+$/u',
            'dni' => ['required', 'string', 'regex:/(V|E|J)-\d{5,9}/'],
            'phone' => ['required', 'string', 'regex:/^\+58(412|414|424|416|426|422)\d{7}$/'],
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'address_description' => 'required|string|not_regex:/[\{\}\<\>]/',
            'status' => 'sometimes|required|string|in:recibiendo_insumos,capacidad_maxima,distribuyendo,en_traslado,inactivo',
            'audit_name' => 'required|string|max:150|regex:/^[\pL\s\-]+$/u',
            'audit_phone' => 'required|string|max:20',
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

        $user = auth()->user();
        $isSuperadmin = $user && $user->role === 'superadmin';
        $isCoordinator = $user && $user->role === 'coordinator';

        // Superadmin puede editar todo sin secure_token
        if ($isSuperadmin) {
            $validator = Validator::make($request->all(), [
                'center_name' => 'sometimes|required|string|max:150|regex:/^[\pL\s\-]+$/u',
                'manager_name' => 'sometimes|required|string|max:150|regex:/^[\pL\s\-]+$/u',
                'phone' => ['sometimes', 'required', 'string', 'regex:/^\+58(412|414|424|416|426|422)\d{7}$/'],
                'latitude' => 'sometimes|required|numeric',
                'longitude' => 'sometimes|required|numeric',
                'address_description' => 'sometimes|required|string|not_regex:/[\{\}\<\>]/',
                'status' => 'sometimes|required|string|in:recibiendo_insumos,capacidad_maxima,distribuyendo,en_traslado,inactivo',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }

            $center->update($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'Centro actualizado por administrador',
                'data' => $center
            ]);
        }

        // Coordinator puede editar: coordenadas, dirección, estado
        if ($isCoordinator) {
            $validator = Validator::make($request->all(), [
                'latitude' => 'sometimes|required|numeric',
                'longitude' => 'sometimes|required|numeric',
                'address_description' => 'sometimes|required|string|not_regex:/[\{\}\<\>]/',
                'status' => 'sometimes|required|string|in:recibiendo_insumos,capacidad_maxima,distribuyendo,en_traslado,inactivo',
                // Coordinator NO puede cambiar estos campos
                'center_name' => 'prohibited',
                'manager_name' => 'prohibited',
                'dni' => 'prohibited',
                'phone' => 'prohibited',
            ], [
                'center_name.prohibited' => 'El nombre del centro no puede ser modificado.',
                'manager_name.prohibited' => 'El nombre del encargado no puede ser modificado.',
                'dni.prohibited' => 'El DNI no puede ser modificado después de la creación (Inmutabilidad).',
                'phone.prohibited' => 'El teléfono no puede ser modificado (Inmutabilidad).'
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }

            $center->update($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'Centro actualizado por coordinador',
                'data' => $center
            ]);
        }

        // Usuarios normales necesitan secure_token
        if ($request->header('X-Secure-Token') !== $center->secure_token) {
            return response()->json(['success' => false, 'message' => 'Token de seguridad inválido'], 403);
        }

        $validator = Validator::make($request->all(), [
            'center_name' => 'sometimes|required|string|max:150|regex:/^[\pL\s\-]+$/u',
            'manager_name' => 'sometimes|required|string|max:150|regex:/^[\pL\s\-]+$/u',
            'latitude' => 'sometimes|required|numeric',
            'longitude' => 'sometimes|required|numeric',
            'address_description' => 'sometimes|required|string|not_regex:/[\{\}\<\>]/',
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

        $user = auth()->user();

        // Solo superadmin puede eliminar sin token
        if ($user && $user->role === 'superadmin') {
            $center->delete();
            return response()->json([
                'success' => true,
                'message' => 'Centro eliminado por administrador'
            ]);
        }

        // Otros usuarios necesitan secure_token
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
