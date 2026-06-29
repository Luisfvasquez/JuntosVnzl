<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\LostPet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class LostPetController extends Controller
{
    public function index()
    {
        $pets = LostPet::all();

        return response()->json([
            'success' => true,
            'data' => $pets,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reporter_name' => 'required|string|max:150|regex:/^[\pL\s\-]+$/u',
            'contact_phone' => ['required', 'string', 'regex:/^\+58(412|414|424|416|426)\d{7}$/'],
            'pet_name' => 'nullable|string|max:100|regex:/^[\pL\s\-]+$/u',
            'species' => 'nullable|string|in:perro,gato,otro',
            'breed' => 'nullable|string|max:100|not_regex:/[\{\}\<\>]/',
            'description' => 'nullable|string|not_regex:/[\{\}\<\>]/',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'address_description' => 'nullable|string|not_regex:/[\{\}\<\>]/',
            'photo' => 'nullable|image|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        if ($request->hasFile('photo')) {
            $manager = new ImageManager(new Driver);
            $imageFile = $request->file('photo');
            $image = $manager->decode($imageFile);
            $encoded = $image->encodeUsingFileExtension('webp', 80); // Convert to WebP with 80% quality

            $filename = 'lost_pets/'.uniqid().'.webp';
            Storage::disk('r2')->put($filename, (string) $encoded);

            $data['photo_url'] = Storage::disk('r2')->url($filename);
        }

        $pet = LostPet::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Mascota perdida registrada exitosamente',
            'data' => $pet,
        ], 201);
    }

    public function show($id)
    {
        $pet = LostPet::find($id);
        if (! $pet) {
            return response()->json(['success' => false, 'message' => 'Mascota no encontrada'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $pet,
        ]);
    }

    public function update(Request $request, $id)
    {
        $pet = LostPet::find($id);
        if (! $pet) {
            return response()->json(['success' => false, 'message' => 'Mascota no encontrada'], 404);
        }

        if ($request->header('X-Secure-Token') !== $pet->secure_token) {
            return response()->json(['success' => false, 'message' => 'Token de seguridad inválido'], 403);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'sometimes|required|string|in:perdido,encontrado,en_refugio',
            'contact_phone' => 'prohibited',
        ], [
            'contact_phone.prohibited' => 'El teléfono de contacto no puede ser modificado tras la creación.',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $pet->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Mascota actualizada',
            'data' => $pet,
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $pet = LostPet::find($id);
        if (! $pet) {
            return response()->json(['success' => false, 'message' => 'Mascota no encontrada'], 404);
        }

        if ($request->header('X-Secure-Token') !== $pet->secure_token) {
            return response()->json(['success' => false, 'message' => 'Token de seguridad inválido'], 403);
        }

        $pet->delete();

        return response()->json([
            'success' => true,
            'message' => 'Registro eliminado exitosamente',
        ]);
    }
}
