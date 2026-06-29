<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Hospital;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class HospitalController extends Controller
{
    public function index(Request $request)
    {
        if ($request->has('page') || $request->has('per_page')) {
            $hospitals = Hospital::paginate($request->query('per_page', 15));
        } else {
            $hospitals = Hospital::all();
        }

        return response()->json([
            'success' => true,
            'data' => $hospitals
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:150|regex:/^[\pL\s\-]+$/u',
            'type' => 'nullable|string|in:hospital_publico,clinica_privada,carpa_campana',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'address_description' => 'required|string|not_regex:/[\{\}\<\>]/',
            'status' => 'nullable|string|in:operativo,colapsado,sin_insumos,evacuado',
            'contact_phone' => 'nullable|string|max:20',
            'image' => 'nullable|image|max:5120' // Max 5MB
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // Validación de duplicidad (por nombre o coordenadas)
        $exists = Hospital::where('name', $request->name)
                          ->orWhere(function($query) use ($request) {
                              $query->where('latitude', $request->latitude)
                                    ->where('longitude', $request->longitude);
                          })->exists();

        if ($exists) {
            return response()->json(['success' => false, 'message' => 'El hospital ya se encuentra registrado o hay uno en las mismas coordenadas exactas.'], 409);
        }

        $data = $validator->validated();

        if ($request->hasFile('image')) {
            $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
            $imageFile = $request->file('image');
            $image = $manager->decode($imageFile);
            $encoded = $image->encodeUsingFileExtension('webp', 80); // Convert to WebP with 80% quality
            
            $filename = 'hospitals/' . uniqid() . '.webp';
            Storage::disk('r2')->put($filename, (string) $encoded);
            
            $data['image_url'] = Storage::disk('r2')->url($filename);
        }

        $hospital = Hospital::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Hospital registrado exitosamente',
            'data' => $hospital
        ], 201);
    }
}
