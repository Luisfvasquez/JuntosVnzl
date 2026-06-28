<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Evacuee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class EvacueeController extends Controller
{
    public function index(Request $request)
    {
        $query = Evacuee::query();

        if ($request->filled('dni')) {
            $query->where('dni', $request->dni);
        }

        if ($request->filled('name')) {
            $name = $request->name;
            $query->where(function ($q) use ($name) {
                $q->where('first_name', 'ILIKE', "%$name%")
                  ->orWhere('last_name', 'ILIKE', "%$name%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('current_location')) {
            $query->where('current_location', 'ILIKE', '%' . $request->current_location . '%');
        }

        return response()->json([
            'success' => true,
            'data' => $query->orderBy('created_at', 'desc')->paginate($request->query('per_page', 15)),
        ]);
    }

    public function show($id)
    {
        $evacuee = Evacuee::find($id);

        if (!$evacuee) {
            return response()->json(['success' => false, 'message' => 'Damnificado no encontrado'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $evacuee
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'dni' => 'nullable|string|max:50',
            'age' => 'nullable|integer',
            'gender' => 'nullable|string|in:masculino,femenino,otro',
            'current_location' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'origin_address' => 'nullable|string',
            'special_conditions' => 'nullable|string',
            'minor_medical_conditions' => 'nullable|string',
            'immediate_needs' => 'nullable|string',
            'dependents_count' => 'nullable|integer',
            'looking_for' => 'nullable|string',
            'status' => 'nullable|string|in:en_refugio,reubicado,desaparecido,fallecido',
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
            $encoded = $image->encodeUsingFileExtension('webp', 80);

            $filename = 'evacuees/'.uniqid().'.webp';
            Storage::disk('r2')->put($filename, (string) $encoded);

            $data['photo_url'] = Storage::disk('r2')->url($filename);
        }

        $evacuee = Evacuee::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Damnificado registrado exitosamente',
            'data' => $evacuee,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $user = auth('api')->user() ?: auth()->user();
        if (!$user || !in_array($user->role, ['superadmin', 'admin', 'coordinator'])) {
            return response()->json(['success' => false, 'message' => 'No autorizado para editar damnificados.'], 403);
        }

        $evacuee = Evacuee::find($id);

        if (!$evacuee) {
            return response()->json(['success' => false, 'message' => 'Damnificado no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'first_name' => 'nullable|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'dni' => 'nullable|string|max:50',
            'age' => 'nullable|integer',
            'gender' => 'nullable|string|in:masculino,femenino,otro',
            'current_location' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'origin_address' => 'nullable|string',
            'special_conditions' => 'nullable|string',
            'minor_medical_conditions' => 'nullable|string',
            'immediate_needs' => 'nullable|string',
            'dependents_count' => 'nullable|integer',
            'looking_for' => 'nullable|string',
            'status' => 'nullable|string|in:en_refugio,reubicado,desaparecido,fallecido',
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
            $encoded = $image->encodeUsingFileExtension('webp', 80);

            $filename = 'evacuees/'.uniqid().'.webp';
            Storage::disk('r2')->put($filename, (string) $encoded);

            $data['photo_url'] = Storage::disk('r2')->url($filename);
        }

        $evacuee->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Damnificado actualizado exitosamente',
            'data' => $evacuee,
        ]);
    }

    public function destroy($id)
    {
        $user = auth('api')->user() ?: auth()->user();
        if (!$user || !in_array($user->role, ['superadmin', 'admin'])) {
            return response()->json(['success' => false, 'message' => 'No autorizado para eliminar.'], 403);
        }

        $evacuee = Evacuee::find($id);

        if (!$evacuee) {
            return response()->json(['success' => false, 'message' => 'Damnificado no encontrado'], 404);
        }

        $evacuee->delete();

        return response()->json([
            'success' => true,
            'message' => 'Damnificado eliminado exitosamente'
        ]);
    }
}
