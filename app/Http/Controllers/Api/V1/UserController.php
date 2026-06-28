<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Verificar si el usuario autenticado tiene permisos de administrador
     */
    private function isAdmin()
    {
        $user = auth('api')->user();
        return $user && in_array($user->role, ['superadmin', 'admin', 'coordinator']);
    }

    /**
     * Listar todos los usuarios registrados (Solo Administradores)
     */
    public function index(Request $request)
    {
        if (!$this->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para realizar esta acción'
            ], 403);
        }

        // Permitir filtrado por rol o búsqueda por nombre/cedula/email
        $query = User::query();

        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('dni', 'like', "%{$search}%");
            });
        }

        // Ordenar por defecto los más recientes primero
        $users = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    /**
     * Obtener los datos de un usuario específico (Solo Administradores)
     */
    public function show($id)
    {
        if (!$this->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para realizar esta acción'
            ], 403);
        }

        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    /**
     * Actualizar los datos y el rol de un usuario (Solo Administradores)
     */
    public function update(Request $request, $id)
    {
        if (!$this->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para realizar esta acción'
            ], 403);
        }

        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:150',
            'dni' => [
                'sometimes',
                'required',
                'string',
                'regex:/(V|E|J)-\d{5,9}/',
                Rule::unique('users')->ignore($user->id),
            ],
            'phone' => [
                'sometimes',
                'required',
                'string',
                'regex:/^\+58(412|414|424|416|426)\d{7}$/',
                Rule::unique('users')->ignore($user->id),
            ],
            'email' => [
                'sometimes',
                'required',
                'string',
                'email',
                'max:150',
                Rule::unique('users')->ignore($user->id),
            ],
            'password' => 'nullable|string|min:6',
            'role' => 'sometimes|required|string|in:superadmin,admin,coordinator,engineer,volunteer,user',
            'is_verified' => 'sometimes|boolean'
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'dni.unique' => 'Esta cédula ya está en uso por otro usuario.',
            'email.unique' => 'Este correo ya está en uso por otro usuario.',
            'phone.unique' => 'Este teléfono ya está en uso.',
            'role.in' => 'El rol seleccionado no es válido.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->only(['name', 'dni', 'phone', 'email', 'role', 'is_verified']);
        
        // Si se envió una nueva contraseña y no está vacía, la actualizamos
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Usuario actualizado exitosamente',
            'data' => $user
        ]);
    }

    /**
     * Eliminar un usuario (Solo Administradores)
     */
    public function destroy($id)
    {
        if (!$this->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para realizar esta acción'
            ], 403);
        }

        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        // Evitar que un administrador se elimine a sí mismo
        if (auth('api')->id() == $id) {
            return response()->json([
                'success' => false,
                'message' => 'No puedes eliminar tu propia cuenta'
            ], 403);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Usuario eliminado exitosamente'
        ]);
    }
}
