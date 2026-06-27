<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Volunteer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class VolunteerController extends Controller
{
    public function index(Request $request)
    {
        $query = Volunteer::query();

        if ($request->filled('skill')) {
            $query->where('skill_category', $request->skill);
        }

        if ($request->filled('modality')) {
            $query->where('modality', $request->modality);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                // ILIKE es específico de Postgres para case-insensitive
                $q->where('full_name', 'ILIKE', "%{$search}%")
                  ->orWhere('location_description', 'ILIKE', "%{$search}%");
            });
        }

        return response()->json([
            'success' => true,
            'data' => $query->get()
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:150',
            'dni' => 'required|string|max:20',
            'phone' => ['required', 'string', 'regex:/^\+58(412|414|424|416|426)\d{7}$/'],
            'email' => 'nullable|email|max:150',
            'skill_category' => 'required|string|max:50',
            'availability_description' => 'required|string|max:150',
            'modality' => 'required|string|in:presencial,virtual',
            'location_description' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'description' => 'required|string',
            'photo' => 'nullable|image|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        if ($request->hasFile('photo')) {
            $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
            $imageFile = $request->file('photo');
            $image = $manager->decode($imageFile);
            $encoded = $image->encodeUsingFileExtension('webp', 80);
            
            $filename = 'volunteers/' . uniqid() . '.webp';
            Storage::disk('r2')->put($filename, (string) $encoded);
            
            $data['photo_url'] = Storage::disk('r2')->url($filename);
        }

        $volunteer = Volunteer::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Voluntario registrado exitosamente',
            'data' => $volunteer
        ], 201);
    }

    public function show($id)
    {
        $volunteer = Volunteer::find($id);
        if (!$volunteer) {
            return response()->json(['success' => false, 'message' => 'Voluntario no encontrado'], 404);
        }
        return response()->json([
            'success' => true,
            'data' => $volunteer
        ]);
    }

    public function update(Request $request, $id)
    {
        $volunteer = Volunteer::find($id);
        if (!$volunteer) {
            return response()->json(['success' => false, 'message' => 'Voluntario no encontrado'], 404);
        }

        if ($request->header('X-Secure-Token') !== $volunteer->secure_token) {
            return response()->json(['success' => false, 'message' => 'Token de seguridad inválido'], 403);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'sometimes|required|string|in:disponible,asignado,inactivo',
            'availability_description' => 'sometimes|required|string|max:150',
            'location_description' => 'sometimes|required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'dni' => 'prohibited',
            'phone' => 'prohibited'
        ], [
            'dni.prohibited' => 'El DNI no puede ser modificado.',
            'phone.prohibited' => 'El teléfono de contacto no puede ser modificado tras la creación.'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $volunteer->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Voluntario actualizado',
            'data' => $volunteer
        ]);
    }
}
