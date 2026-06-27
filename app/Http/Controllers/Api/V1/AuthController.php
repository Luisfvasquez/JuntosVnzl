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
