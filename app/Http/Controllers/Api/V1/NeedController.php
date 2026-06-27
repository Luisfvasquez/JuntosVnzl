<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Need;
use App\Models\NeedResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class NeedController extends Controller
{
    public function index(Request $request)
    {
        $query = Need::query();

        if ($request->has('status')) {
            $query->where('status', $request->query('status'));
        }

        $perPage = $request->query('per_page', 15);
        $needs = $query->latest()->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Necesidades obtenidas',
            'data' => $needs
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reporter_name' => 'nullable|string|max:150',
            'phone' => 'nullable|string|max:50',
            'items_list' => 'required|array',
            'items_list.*' => 'string',
            'description' => 'nullable|string',
            'location' => 'required|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'photos' => 'nullable|array',
            'photos.*' => 'image|max:20480',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        $photoUrls = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('needs', 'r2');
                $photoUrls[] = Storage::disk('r2')->url($path);
            }
        }
        
        unset($data['photos']);
        if (!empty($photoUrls)) {
            $data['photos'] = $photoUrls;
        }

        $data['secure_token'] = Str::uuid()->toString();

        $need = Need::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Necesidad publicada exitosamente',
            'data' => $need
        ], 201);
    }

    public function updateStatus(Request $request, $id)
    {
        $need = Need::find($id);
        if (!$need) {
            return response()->json(['success' => false, 'message' => 'Necesidad no encontrada'], 404);
        }

        $validator = Validator::make($request->all(), [
            'secure_token' => 'nullable|string',
            'status' => 'required|in:activa,resuelta'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $isAuth = auth('api')->check();
        $tokenMatch = $request->filled('secure_token') && $need->secure_token === $request->input('secure_token');

        if (!$isAuth && !$tokenMatch) {
            return response()->json(['success' => false, 'message' => 'Token de seguridad inválido o requiere login'], 403);
        }

        $need->status = $request->input('status');
        $need->save();

        return response()->json([
            'success' => true,
            'message' => 'Estado actualizado exitosamente',
            'data' => $need
        ]);
    }

    public function getResponses(Request $request, $id)
    {
        $need = Need::find($id);
        if (!$need) {
            return response()->json(['success' => false, 'message' => 'Necesidad no encontrada'], 404);
        }

        $perPage = $request->query('per_page', 10);
        $responses = $need->responses()->latest()->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $responses
        ]);
    }

    public function storeResponse(Request $request, $id)
    {
        $need = Need::find($id);
        if (!$need) {
            return response()->json(['success' => false, 'message' => 'Necesidad no encontrada'], 404);
        }

        $validator = Validator::make($request->all(), [
            'responder_name' => 'required|string|max:150',
            'contact_info' => 'required|string',
            'location' => 'nullable|string',
            'items_offered' => 'required|array',
            'items_offered.*' => 'string',
            'photos' => 'nullable|array',
            'photos.*' => 'image|max:20480',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        $photoUrls = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('need_responses', 'r2');
                $photoUrls[] = Storage::disk('r2')->url($path);
            }
        }
        
        unset($data['photos']);
        if (!empty($photoUrls)) {
            $data['photos'] = $photoUrls;
        }

        $data['secure_token'] = Str::uuid()->toString();
        $data['need_id'] = $need->id;

        $response = NeedResponse::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Respuesta publicada exitosamente',
            'data' => $response
        ], 201);
    }
}
