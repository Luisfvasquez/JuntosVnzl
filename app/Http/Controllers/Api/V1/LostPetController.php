<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\LostPet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class LostPetController extends Controller
{
    public function index()
    {
        $pets = LostPet::all();
        return response()->json([
            'success' => true,
            'data' => $pets
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reporter_name' => 'required|string|max:150',
            'contact_phone' => ['required', 'string', 'regex:/^\+58(412|414|424|416|426)\d{7}$/'],
            'pet_name' => 'nullable|string|max:100',
            'species' => 'nullable|string|in:perro,gato,otro',
            'breed' => 'nullable|string|max:100',
            'description' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'address_description' => 'required|string',
            'photo' => 'nullable|image|max:5120'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('lost_pets', 'r2');
            $data['photo_url'] = Storage::disk('r2')->url($path);
        }

        $pet = LostPet::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Mascota perdida registrada exitosamente',
            'data' => $pet
        ], 201);
    }

    public function show($id)
    {
        $pet = LostPet::find($id);
        if (!$pet) {
            return response()->json(['success' => false, 'message' => 'Mascota no encontrada'], 404);
        }
        return response()->json([
            'success' => true,
            'data' => $pet
        ]);
    }

    public function update(Request $request, $id)
    {
        $pet = LostPet::find($id);
        if (!$pet) {
            return response()->json(['success' => false, 'message' => 'Mascota no encontrada'], 404);
        }

        if ($request->header('X-Secure-Token') !== $pet->secure_token) {
            return response()->json(['success' => false, 'message' => 'Token de seguridad inválido'], 403);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'sometimes|required|string|in:perdido,encontrado,en_refugio',
            'contact_phone' => 'prohibited'
        ], [
            'contact_phone.prohibited' => 'El teléfono de contacto no puede ser modificado tras la creación.'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $pet->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Mascota actualizada',
            'data' => $pet
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $pet = LostPet::find($id);
        if (!$pet) {
            return response()->json(['success' => false, 'message' => 'Mascota no encontrada'], 404);
        }

        if ($request->header('X-Secure-Token') !== $pet->secure_token) {
            return response()->json(['success' => false, 'message' => 'Token de seguridad inválido'], 403);
        }

        $pet->delete();

        return response()->json([
            'success' => true,
            'message' => 'Registro eliminado exitosamente'
        ]);
    }
}
