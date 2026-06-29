<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class PatientController extends Controller
{
    public function search(Request $request)
    {
        $query = Patient::with('hospital');

        if ($request->filled('dni')) {
            $dni = trim($request->dni);
            $digits = preg_replace('/[^0-9]/', '', $dni);
            $query->where(function ($q) use ($dni, $digits) {
                $q->where('dni', 'ILIKE', "%$dni%");
                if (!empty($digits)) {
                    $q->orWhere(DB::raw("regexp_replace(dni, '[^0-9]', '', 'g')"), 'LIKE', "%$digits%");
                }
            });
        }

        if ($request->filled('name')) {
            $name = trim($request->name);
            $digits = preg_replace('/[^0-9]/', '', $name);
            $query->where(function ($q) use ($name, $digits) {
                $q->where('first_name', 'ILIKE', "%$name%")
                    ->orWhere('last_name', 'ILIKE', "%$name%")
                    ->orWhere('dni', 'ILIKE', "%$name%")
                    ->orWhere('physical_description', 'ILIKE', "%$name%");
                if (!empty($digits)) {
                    $q->orWhere(DB::raw("regexp_replace(dni, '[^0-9]', '', 'g')"), 'LIKE', "%$digits%");
                }
            });
        }

        if ($request->filled('description')) {
            $query->where('physical_description', 'ILIKE', '%'.$request->description.'%');
        }

        if ($request->has('hospital_id')) {
            $hospitalIds = is_array($request->hospital_id) 
                ? $request->hospital_id 
                : array_map('trim', explode(',', $request->hospital_id));
                
            $query->whereIn('hospital_id', array_filter($hospitalIds));
        }

        if ($request->has('medical_status')) {
            $statuses = is_array($request->medical_status)
                ? $request->medical_status
                : array_map('trim', explode(',', $request->medical_status));
            
            $query->whereIn('medical_status', array_filter($statuses));
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
            'hospital_area' => 'nullable|string|max:150|not_regex:/[\{\}\<\>]/',
            'dni' => 'nullable|string|max:20',
            'first_name' => 'nullable|string|max:100|regex:/^[\pL\s\-]+$/u',
            'last_name' => 'nullable|string|max:100|regex:/^[\pL\s\-]+$/u',
            'gender' => 'nullable|string|in:masculino,femenino,otro',
            'approximate_age' => 'nullable|integer|min:0',
            'physical_description' => 'nullable|string|not_regex:/[\{\}\<\>]/',
            'medical_status' => 'nullable|string|in:estable,critico,observacion,dado_de_alta,fallecido,trasladado,desconocido',
            'photo' => 'nullable|image|max:5120',
            'arrival_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        if (! empty($request->dni)) {
            $exists = Patient::where('dni', $request->dni)->exists();
            if ($exists) {
                return response()->json(['success' => false, 'message' => 'Ya existe un paciente registrado con ese DNI.'], 409);
            }
        }

        $data = $validator->validated();

        if ($request->hasFile('photo')) {
            $manager = new ImageManager(new Driver);
            $imageFile = $request->file('photo');
            $image = $manager->decode($imageFile);
            $encoded = $image->encodeUsingFileExtension('webp', 80); // Convert to WebP with 80% quality

            $filename = 'patients/'.uniqid().'.webp';
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
        $user = auth()->user();
        if (!in_array($user->role, ['superadmin', 'coordinator'])) {
            return response()->json(['success' => false, 'message' => 'No autorizado para editar pacientes.'], 403);
        }

        $patient = Patient::find($id);

        if (! $patient) {
            return response()->json(['success' => false, 'message' => 'Paciente no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'first_name' => 'nullable|string|max:100|regex:/^[\pL\s\-]+$/u',
            'last_name' => 'nullable|string|max:100|regex:/^[\pL\s\-]+$/u',
            'gender' => 'nullable|string|in:masculino,femenino,otro',
            'approximate_age' => 'nullable|integer|min:0',
            'physical_description' => 'nullable|string|not_regex:/[\{\}\<\>]/',
            'medical_status' => 'nullable|string|in:estable,critico,observacion,dado_de_alta,fallecido,trasladado,desconocido',
            'hospital_id' => 'required|exists:hospitals,id',
            'hospital_area' => 'nullable|string|max:150|not_regex:/[\{\}\<\>]/',
            'dni' => 'nullable|string|max:50',
            'photo' => 'nullable|image|max:5120',
            'arrival_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        if (!empty($request->dni) && $request->dni !== $patient->dni) {
            $exists = Patient::where('dni', $request->dni)->exists();
            if ($exists) {
                return response()->json(['success' => false, 'message' => 'Ya existe un paciente registrado con ese DNI.'], 409);
            }
        }

        $data = $validator->validated();

        if ($request->hasFile('photo')) {
            $manager = new ImageManager(new Driver);
            $imageFile = $request->file('photo');
            $image = $manager->decode($imageFile);
            $encoded = $image->encodeUsingFileExtension('webp', 80);

            $filename = 'patients/'.uniqid().'.webp';
            Storage::disk('r2')->put($filename, (string) $encoded);

            $data['photo_url'] = Storage::disk('r2')->url($filename);
        }

        $patient->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Paciente actualizado exitosamente',
            'data' => $patient,
        ]);
    }

    public function destroy($id)
    {
        $user = auth()->user();
        if ($user->role !== 'superadmin') {
            return response()->json(['success' => false, 'message' => 'No autorizado para eliminar pacientes.'], 403);
        }

        $patient = Patient::find($id);

        if (! $patient) {
            return response()->json(['success' => false, 'message' => 'Paciente no encontrado'], 404);
        }

        $patient->delete();

        return response()->json([
            'success' => true,
            'message' => 'Paciente eliminado exitosamente'
        ]);
    }
}
