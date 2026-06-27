<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PatientController extends Controller
{
    public function search(Request $request)
    {
        $query = Patient::with('hospital');

        if ($request->filled('dni')) {
            $query->where('dni', $request->dni);
        }

        if ($request->filled('name')) {
            $name = $request->name;
            $query->where(function ($q) use ($name) {
                // ILIKE es específico de Postgres para búsquedas insensibles a mayúsculas
                $q->where('first_name', 'ILIKE', "%$name%")
                    ->orWhere('last_name', 'ILIKE', "%$name%");
            });
        }

        if ($request->filled('description')) {
            $query->where('physical_description', 'ILIKE', '%'.$request->description.'%');
        }

        return response()->json([
            'success' => true,
            'data' => $query->paginate($request->query('per_page', 15)),
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'hospital_id' => 'required|exists:hospitals,id',
            'dni' => 'nullable|string|max:20',
            'first_name' => 'nullable|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'approximate_age' => 'nullable|integer',
            'physical_description' => 'nullable|string',
            'medical_status' => 'nullable|string|in:estable,critico,observacion,dado_de_alta,fallecido,trasladado,desconocido',
            'photo' => 'nullable|image|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        if (! empty($request->dni)) {
            $exists = Patient::where('dni', $request->dni)->exists();
            if ($exists) {
                return response()->json(['success' => false, 'message' => 'Ya existe un paciente registrado con ese DNI.'], 409);
            }
        } elseif (! empty($request->first_name) && ! empty($request->last_name)) {
            $exists = Patient::where('first_name', $request->first_name)
                ->where('last_name', $request->last_name)
                ->where('hospital_id', $request->hospital_id)
                ->exists();
            if ($exists) {
                return response()->json(['success' => false, 'message' => 'Ya existe un paciente con ese nombre en este hospital.'], 409);
            }
        }

        $data = $validator->validated();

        if ($request->hasFile('photo')) {
            $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
            $imageFile = $request->file('photo');
            $image = $manager->read($imageFile);
            $encoded = $image->toWebp(80); // Convert to WebP with 80% quality
            
            $filename = 'patients/' . uniqid() . '.webp';
            Storage::disk('r2')->put($filename, (string) $encoded);
            
            $data['photo_url'] = Storage::disk('r2')->url($filename);
        }

        $patient = Patient::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Paciente registrado exitosamente',
            'data' => $patient,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $patient = Patient::find($id);
        
        if (!$patient) {
            return response()->json(['success' => false, 'message' => 'Paciente no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'first_name' => 'sometimes|nullable|string|max:100',
            'last_name' => 'sometimes|nullable|string|max:100',
            'approximate_age' => 'sometimes|nullable|integer',
            'physical_description' => 'sometimes|nullable|string',
            'medical_status' => 'sometimes|nullable|string|in:estable,critico,observacion,dado_de_alta,fallecido,trasladado',
            'dni' => 'prohibited',
            'hospital_id' => 'prohibited'
        ], [
            'dni.prohibited' => 'El DNI no puede ser modificado.',
            'hospital_id.prohibited' => 'El hospital no puede ser modificado por este medio.'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $patient->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Paciente actualizado exitosamente por usuario registrado',
            'data' => $patient
        ]);
    }
}
