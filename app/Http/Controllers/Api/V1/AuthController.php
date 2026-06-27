<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:150',
            'dni' => ['required', 'string', 'regex:/(V|E|J)-\d{5,9}/', 'unique:users'],
            'phone' => ['required', 'string', 'regex:/^\+58(412|414|424|416|426)\d{7}$/', 'unique:users'],
            'email' => 'required|string|email|max:150|unique:users',
            'password' => 'required|string|min:6',
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'name.max' => 'El nombre no debe exceder los 150 caracteres.',
            'dni.required' => 'La cédula/DNI es obligatoria.',
            'dni.regex' => 'El formato de la cédula/DNI es inválido (ej. V-12345678).',
            'dni.unique' => 'Esta cédula/DNI ya se encuentra registrada.',
            'phone.required' => 'El teléfono es obligatorio.',
            'phone.regex' => 'El teléfono debe tener un formato válido de Venezuela (ej. +584121234567).',
            'phone.unique' => 'Este número de teléfono ya se encuentra registrado.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico no es válido.',
            'email.unique' => 'Este correo electrónico ya se encuentra registrado.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'dni' => $request->dni,
            'phone' => $request->phone,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'volunteer',
        ]);

        // Aquí iría el evento/observer para la migración de recursos huérfanos (Fase II)

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'success' => true,
            'message' => 'Usuario registrado exitosamente',
            'data' => compact('user', 'token')
        ], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (! $token = auth('api')->attempt($credentials)) {
            return response()->json(['success' => false, 'message' => 'Credenciales inválidas'], 401);
        }

        return response()->json([
            'success' => true,
            'message' => 'Login exitoso',
            'data' => [
                'token' => $token,
                'user' => auth('api')->user()
            ]
        ]);
    }
}
