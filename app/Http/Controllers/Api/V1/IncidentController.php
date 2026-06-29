<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Incident;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class IncidentController extends Controller
{
    public function index(Request $request)
    {
        if ($request->has('page') || $request->has('per_page')) {
            $incidents = Incident::paginate($request->query('per_page', 15));
        } else {
            $incidents = Incident::all();
        }

        return response()->json([
            'success' => true,
            'message' => 'Incidentes obtenidos',
            'data' => $incidents
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reporter_name' => 'nullable|string|max:150|regex:/^[\pL\s\-]+$/u',
            'description' => 'required|string|not_regex:/[\{\}\<\>]/',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'address_description' => 'required|string|not_regex:/[\{\}\<\>]/',
            'severity' => 'nullable|string|in:leve,moderado,critico,catastrofico',
            'photo' => 'nullable|image|max:5120',
            'audit_name' => 'required|string|max:150|regex:/^[\pL\s\-]+$/u',
            'audit_phone' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('incidents', 'r2');
            $data['photo_url'] = Storage::disk('r2')->url($path);
        }

        $incident = Incident::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Incidente reportado exitosamente',
            'data' => $incident
        ], 201);
    }

    public function show($id)
    {
        $incident = Incident::find($id);
        if (!$incident) {
            return response()->json(['success' => false, 'message' => 'Incidente no encontrado'], 404);
        }
        return response()->json([
            'success' => true,
            'data' => $incident
        ]);
    }
}
