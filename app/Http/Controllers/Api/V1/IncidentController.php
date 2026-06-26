<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Incident;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class IncidentController extends Controller
{
    public function index()
    {
        $incidents = Incident::all();
        return response()->json([
            'success' => true,
            'message' => 'Incidentes obtenidos',
            'data' => $incidents
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reporter_name' => 'nullable|string|max:150',
            'description' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'address_description' => 'required|string',
            'severity' => 'nullable|string|in:leve,moderado,critico,catastrofico',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $incident = Incident::create($validator->validated());

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
