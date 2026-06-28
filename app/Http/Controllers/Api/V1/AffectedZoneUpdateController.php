<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\AffectedZone;
use App\Models\AffectedZoneUpdate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class AffectedZoneUpdateController extends Controller
{
    public function index($zoneId)
    {
        $zone = AffectedZone::find($zoneId);
        if (!$zone) {
            return response()->json(['success' => false, 'message' => 'Zona afectada no encontrada'], 404);
        }

        $updates = AffectedZoneUpdate::where('affected_zone_id', $zoneId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $updates,
        ]);
    }

    public function store(Request $request, $zoneId)
    {
        $zone = AffectedZone::find($zoneId);
        if (!$zone) {
            return response()->json(['success' => false, 'message' => 'Zona afectada no encontrada'], 404);
        }

        $validator = Validator::make($request->all(), [
            'author_name' => 'nullable|string|max:100',
            'message' => 'required|string',
            'is_attended_change' => 'nullable|boolean',
            'status_change' => 'nullable|string|in:activa,controlada,evacuada',
            'photo' => 'nullable|image|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $data['affected_zone_id'] = $zoneId;

        // Auto-assign authenticated user details if logged in
        $user = auth('api')->user();
        if ($user) {
            $data['user_id'] = $user->id;
            if (empty($data['author_name'])) {
                $data['author_name'] = $user->name;
            }
        } else {
            // Guest must provide author name
            if (empty($data['author_name'])) {
                $data['author_name'] = 'Anónimo';
            }
        }

        // Image upload to Cloudflare R2
        if ($request->hasFile('photo')) {
            $manager = new ImageManager(new Driver);
            $imageFile = $request->file('photo');
            $image = $manager->decode($imageFile);
            $encoded = $image->encodeUsingFileExtension('webp', 80);

            $filename = 'zone_updates/'.uniqid().'.webp';
            Storage::disk('r2')->put($filename, (string) $encoded);

            $data['image_urls'] = [Storage::disk('r2')->url($filename)];
        }

        $update = AffectedZoneUpdate::create($data);

        // Apply changes to parent zone if requested
        $zoneChanges = [];
        if (isset($data['is_attended_change'])) {
            $zoneChanges['is_attended'] = $data['is_attended_change'];
        }
        if (isset($data['status_change'])) {
            $zoneChanges['status'] = $data['status_change'];
        }

        if (!empty($zoneChanges)) {
            $zone->update($zoneChanges);
        }

        return response()->json([
            'success' => true,
            'message' => 'Actualización registrada exitosamente',
            'data' => $update,
            'zone' => $zone->fresh(),
        ], 201);
    }

    public function destroy($id)
    {
        $user = auth('api')->user() ?: auth()->user();
        if (!$user || !in_array($user->role, ['superadmin', 'admin', 'coordinator'])) {
            return response()->json(['success' => false, 'message' => 'No autorizado para eliminar bitácoras.'], 403);
        }

        $update = AffectedZoneUpdate::find($id);
        if (!$update) {
            return response()->json(['success' => false, 'message' => 'Actualización no encontrada'], 404);
        }

        $update->delete();

        return response()->json([
            'success' => true,
            'message' => 'Actualización eliminada exitosamente'
        ]);
    }
}
