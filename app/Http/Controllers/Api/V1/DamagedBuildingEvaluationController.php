<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\DamagedBuilding;
use App\Models\DamagedBuildingEvaluation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class DamagedBuildingEvaluationController extends Controller
{
    public function index($buildingId)
    {
        $building = DamagedBuilding::find($buildingId);
        if (!$building) {
            return response()->json(['success' => false, 'message' => 'Estructura no encontrada'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $building->evaluations()->get()
        ]);
    }

    public function store(Request $request, $buildingId)
    {
        $building = DamagedBuilding::find($buildingId);
        if (!$building) {
            return response()->json(['success' => false, 'message' => 'Estructura no encontrada'], 404);
        }

        $validator = Validator::make($request->all(), [
            'evaluator_name' => 'required|string|max:255',
            'evaluator_credential' => 'nullable|string|max:100',
            'structural_damage' => 'required|string|max:100',
            'habitability_status' => 'required|string|max:100',
            'collapse_risk' => 'required|string|max:100',
            'recommended_actions' => 'nullable|string',
            'comments' => 'nullable|string',
            'is_verifiable' => 'nullable|boolean',
            'is_verified' => 'nullable|boolean',
            'images' => 'nullable|array|max:10',
            'images.*' => 'image|max:5120'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $data['damaged_building_id'] = $building->id;
        
        $imageUrls = [];
        if ($request->hasFile('images')) {
            $manager = new ImageManager(new Driver);
            foreach ($request->file('images') as $imageFile) {
                $image = $manager->decode($imageFile);
                $encoded = $image->encodeUsingFileExtension('webp', 80);
                $filename = 'damaged-buildings/evaluations/'.uniqid().'.webp';
                Storage::disk('r2')->put($filename, (string) $encoded);
                $imageUrls[] = Storage::disk('r2')->url($filename);
            }
        }
        
        if (!empty($imageUrls)) {
            $data['image_urls'] = $imageUrls;
        }

        $evaluation = DamagedBuildingEvaluation::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Evaluación agregada exitosamente',
            'data' => $evaluation
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $evaluation = DamagedBuildingEvaluation::find($id);
        if (!$evaluation) {
            return response()->json(['success' => false, 'message' => 'Evaluación no encontrada'], 404);
        }

        $validator = Validator::make($request->all(), [
            'evaluator_name' => 'sometimes|required|string|max:255',
            'evaluator_credential' => 'sometimes|nullable|string|max:100',
            'structural_damage' => 'sometimes|required|string|max:100',
            'habitability_status' => 'sometimes|required|string|max:100',
            'collapse_risk' => 'sometimes|required|string|max:100',
            'recommended_actions' => 'sometimes|nullable|string',
            'comments' => 'sometimes|nullable|string',
            'is_verifiable' => 'sometimes|nullable|boolean',
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

        $currentImages = $evaluation->image_urls ?? [];
        
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
                $filename = 'damaged-buildings/evaluations/'.uniqid().'.webp';
                Storage::disk('r2')->put($filename, (string) $encoded);
                $currentImages[] = Storage::disk('r2')->url($filename);
            }
        }
        
        if ($request->has('retained_image_urls') || $request->hasFile('images')) {
            $data['image_urls'] = $currentImages;
        }

        $evaluation->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Evaluación actualizada',
            'data' => $evaluation
        ]);
    }

    public function destroy($id)
    {
        $evaluation = DamagedBuildingEvaluation::find($id);
        if (!$evaluation) {
            return response()->json(['success' => false, 'message' => 'Evaluación no encontrada'], 404);
        }

        $evaluation->delete();

        return response()->json([
            'success' => true,
            'message' => 'Evaluación eliminada'
        ]);
    }
}
