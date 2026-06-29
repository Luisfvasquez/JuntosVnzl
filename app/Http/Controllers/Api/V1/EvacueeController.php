<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Evacuee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
                  ->orWhere('dni', 'ILIKE', "%$name%");
                if (!empty($digits)) {
                    $q->orWhere(DB::raw("regexp_replace(dni, '[^0-9]', '', 'g')"), 'LIKE', "%$digits%");
                }
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
        $evacuee = Evacuee::with(['familyMembers', 'headOfFamily'])->find($id);

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
            'first_name' => 'required|string|max:100|regex:/^[\pL\s\-]+$/u',
            'last_name' => 'nullable|string|max:100|regex:/^[\pL\s\-]+$/u',
            'dni' => 'nullable|string|max:50',
            'age' => 'nullable|integer|min:0',
            'gender' => 'nullable|string|in:masculino,femenino,otro',
            'current_location' => 'nullable|string|not_regex:/[\{\}\<\>]/',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'origin_address' => 'nullable|string|not_regex:/[\{\}\<\>]/',
            'special_conditions' => 'nullable|string|not_regex:/[\{\}\<\>]/',
            'minor_medical_conditions' => 'nullable|string|not_regex:/[\{\}\<\>]/',
            'immediate_needs' => 'nullable|string|not_regex:/[\{\}\<\>]/',
            'dependents_count' => 'nullable|integer|min:0',
            'looking_for' => 'nullable|string|not_regex:/[\{\}\<\>]/',
            'status' => 'nullable|string|in:en_refugio,reubicado,desaparecido,fallecido',
            'photo' => 'nullable|image|max:5120',
            'head_of_family_id' => 'nullable|exists:evacuees,id',
            'family_relationship' => 'nullable|string|max:50',
            'family_members' => 'nullable|array',
            'family_members.*.first_name' => 'required_with:family_members|string|max:100|regex:/^[\pL\s\-]+$/u',
            'family_members.*.last_name' => 'nullable|string|max:100|regex:/^[\pL\s\-]+$/u',
            'family_members.*.dni' => 'nullable|string|max:50',
            'family_members.*.age' => 'nullable|integer|min:0',
            'family_members.*.gender' => 'nullable|string|in:masculino,femenino,otro',
            'family_members.*.family_relationship' => 'nullable|string|max:50',
            'family_members.*.special_conditions' => 'nullable|string|not_regex:/[\{\}\<\>]/',
            'family_members.*.minor_medical_conditions' => 'nullable|string|not_regex:/[\{\}\<\>]/',
            'family_members.*.photo' => 'nullable|image|max:5120',
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

        DB::beginTransaction();
        try {
            // Guardar al principal
            $evacueeData = collect($data)->except('family_members')->toArray();
            $evacuee = Evacuee::create($evacueeData);

            // Guardar a los familiares (si existen)
            if (!empty($data['family_members'])) {
                foreach ($data['family_members'] as $index => $memberData) {
                    $memberData['head_of_family_id'] = $evacuee->id;
                    $memberData['current_location'] = $evacuee->current_location ?? null;
                    $memberData['origin_address'] = $evacuee->origin_address ?? null;
                    $memberData['latitude'] = $evacuee->latitude ?? null;
                    $memberData['longitude'] = $evacuee->longitude ?? null;
                    $memberData['status'] = $evacuee->status ?? null;

                    // Manejo de foto del familiar
                    if ($request->hasFile("family_members.{$index}.photo")) {
                        $manager = new ImageManager(new Driver);
                        $imageFile = $request->file("family_members.{$index}.photo");
                        $image = $manager->decode($imageFile);
                        $encoded = $image->encodeUsingFileExtension('webp', 80);
                        
                        $filename = 'evacuees/'.uniqid().'.webp';
                        Storage::disk('r2')->put($filename, (string) $encoded);
                        $memberData['photo_url'] = Storage::disk('r2')->url($filename);
                    }

                    Evacuee::create($memberData);
                }
            }

            DB::commit();

            $evacuee->load('familyMembers');

            return response()->json([
                'success' => true,
                'message' => 'Damnificado(s) registrado(s) exitosamente',
                'data' => $evacuee,
            ], 201);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar',
                'error' => $e->getMessage()
            ], 500);
        }
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
            'first_name' => 'nullable|string|max:100|regex:/^[\pL\s\-]+$/u',
            'last_name' => 'nullable|string|max:100|regex:/^[\pL\s\-]+$/u',
            'dni' => 'nullable|string|max:50',
            'age' => 'nullable|integer|min:0',
            'gender' => 'nullable|string|in:masculino,femenino,otro',
            'current_location' => 'nullable|string|not_regex:/[\{\}\<\>]/',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'origin_address' => 'nullable|string|not_regex:/[\{\}\<\>]/',
            'special_conditions' => 'nullable|string|not_regex:/[\{\}\<\>]/',
            'minor_medical_conditions' => 'nullable|string|not_regex:/[\{\}\<\>]/',
            'immediate_needs' => 'nullable|string|not_regex:/[\{\}\<\>]/',
            'dependents_count' => 'nullable|integer|min:0',
            'looking_for' => 'nullable|string|not_regex:/[\{\}\<\>]/',
            'status' => 'nullable|string|in:en_refugio,reubicado,desaparecido,fallecido',
            'photo' => 'nullable|image|max:5120',
            'head_of_family_id' => 'nullable|exists:evacuees,id',
            'family_relationship' => 'nullable|string|max:50',
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
