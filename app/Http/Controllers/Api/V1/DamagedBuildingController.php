<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\DamagedBuilding;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class DamagedBuildingController extends Controller
{
    public function index(Request $request)
    {
        $query = DamagedBuilding::with('evaluations');

        if ($request->filled('city')) {
            $query->where('city', 'ILIKE', '%' . $request->city . '%');
        }

        if ($request->filled('damage_level')) {
            $query->where('damage_level', $request->damage_level);
        }

        if ($request->has('page') || $request->has('per_page')) {
            $perPage = $request->input('per_page', 15);
            return response()->json([
                'success' => true,
                'data' => $query->paginate($perPage)
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $query->get()
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|regex:/^[\pL\s\-]+$/u',
            'city' => 'nullable|string|max:150|regex:/^[\pL\s\-]+$/u',
            'zone' => 'nullable|string|max:150|not_regex:/[\{\}\<\>]/',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'damage_level' => 'nullable|string|max:50',
            'status' => 'nullable|string|max:50',
            'general_source' => 'nullable|string|max:255|not_regex:/[\{\}\<\>]/',
            'notes' => 'nullable|string|not_regex:/[\{\}\<\>]/',
            'is_verified' => 'nullable|boolean',
            'images' => 'nullable|array|max:10',
            'images.*' => 'image|max:5120',
            'audit_name' => 'required|string|max:150|regex:/^[\pL\s\-]+$/u',
            'audit_phone' => 'required|string|max:20'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        
        $imageUrls = [];
        if ($request->hasFile('images')) {
            $manager = new ImageManager(new Driver);
            foreach ($request->file('images') as $imageFile) {
                $image = $manager->decode($imageFile);
                $encoded = $image->encodeUsingFileExtension('webp', 80);
                $filename = 'damaged-buildings/'.uniqid().'.webp';
                Storage::disk('r2')->put($filename, (string) $encoded);
                $imageUrls[] = Storage::disk('r2')->url($filename);
            }
        }
        
        if (!empty($imageUrls)) {
            $data['image_urls'] = $imageUrls;
        }

        $building = DamagedBuilding::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Estructura reportada exitosamente',
            'data' => $building
        ], 201);
    }

    public function show($id)
    {
        $building = DamagedBuilding::find($id);

        if (!$building) {
            return response()->json(['success' => false, 'message' => 'Estructura no encontrada'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $building
        ]);
    }

    public function update(Request $request, $id)
    {
        $building = DamagedBuilding::find($id);

        if (!$building) {
            return response()->json(['success' => false, 'message' => 'Estructura no encontrada'], 404);
        }

        if ($request->header('X-Secure-Token') !== $building->secure_token) {
            return response()->json(['success' => false, 'message' => 'Token de seguridad inválido'], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255|regex:/^[\pL\s\-]+$/u',
            'city' => 'sometimes|nullable|string|max:150|regex:/^[\pL\s\-]+$/u',
            'zone' => 'sometimes|nullable|string|max:150|not_regex:/[\{\}\<\>]/',
            'latitude' => 'sometimes|nullable|numeric',
            'longitude' => 'sometimes|nullable|numeric',
            'damage_level' => 'sometimes|nullable|string|max:50',
            'status' => 'sometimes|nullable|string|max:50',
            'general_source' => 'sometimes|nullable|string|max:255|not_regex:/[\{\}\<\>]/',
            'notes' => 'sometimes|nullable|string|not_regex:/[\{\}\<\>]/',
            'is_verified' => 'sometimes|nullable|boolean',
            'retained_image_urls' => 'sometimes|nullable|array',
            'retained_image_urls.*' => 'string',
            'images' => 'sometimes|nullable|array|max:10',
            'images.*' => 'image|max:5120'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        $currentImages = $building->image_urls ?? [];
        
        if ($request->has('retained_image_urls')) {
            $retained = $request->input('retained_image_urls') ?: [];
            $currentImages = array_values(array_intersect($currentImages, $retained));
        }

        if ($request->hasFile('images')) {
            $manager = new ImageManager(new Driver);
            foreach ($request->file('images') as $imageFile) {
                if (count($currentImages) >= 10) break;
                
                $image = $manager->decode($imageFile);
                $encoded = $image->encodeUsingFileExtension('webp', 80);
                $filename = 'damaged-buildings/'.uniqid().'.webp';
                Storage::disk('r2')->put($filename, (string) $encoded);
                $currentImages[] = Storage::disk('r2')->url($filename);
            }
        }
        
        if ($request->has('retained_image_urls') || $request->hasFile('images')) {
            $data['image_urls'] = $currentImages;
        }

        $building->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Estructura actualizada',
            'data' => $building
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $building = DamagedBuilding::find($id);

        if (!$building) {
            return response()->json(['success' => false, 'message' => 'Estructura no encontrada'], 404);
        }

        if ($request->header('X-Secure-Token') !== $building->secure_token) {
            return response()->json(['success' => false, 'message' => 'Token de seguridad inválido'], 403);
        }

        $building->delete();

        return response()->json([
            'success' => true,
            'message' => 'Estructura eliminada'
        ]);
    }
}
