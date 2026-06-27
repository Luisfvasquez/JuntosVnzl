<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAidOfferRequest;
use App\Http\Requests\UpdateAidOfferRequest;
use App\Models\AidOffer;
use Illuminate\Http\Request;

class AidOfferController extends Controller
{
    public function index(Request $request)
    {
        if ($request->has('page') || $request->has('per_page')) {
            $offers = AidOffer::paginate($request->query('per_page', 15));
        } else {
            $offers = AidOffer::all();
        }
        return response()->json([
            'success' => true,
            'message' => 'Ofertas obtenidas correctamente',
            'data' => $offers
        ]);
    }

    public function store(StoreAidOfferRequest $request)
    {
        $offer = AidOffer::create($request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Oferta de ayuda creada exitosamente',
            'data' => $offer
        ], 201);
    }

    public function show($id)
    {
        $offer = AidOffer::find($id);
        if (!$offer) {
            return response()->json(['success' => false, 'message' => 'Oferta no encontrada'], 404);
        }
        return response()->json([
            'success' => true,
            'message' => 'Oferta obtenida',
            'data' => $offer
        ]);
    }

    public function update(Request $request, $id)
    {
        $offer = AidOffer::find($id);
        if (!$offer) {
            return response()->json(['success' => false, 'message' => 'Oferta no encontrada'], 404);
        }

        $user = auth()->user();
        $isSuperadmin = $user && $user->role === 'superadmin';
        $isCoordinator = $user && $user->role === 'coordinator';

        // Superadmin puede editar todo sin secure_token
        if ($isSuperadmin) {
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'provider_name' => 'sometimes|required|string|max:150',
                'dni' => 'sometimes|required|string|max:20',
                'phone' => 'sometimes|required|string|max:20',
                'title' => 'sometimes|required|string|max:200',
                'description' => 'sometimes|required|string',
                'category' => 'sometimes|required|string|in:comida,ropa,vivienda,salud,transporte,otros',
                'quantity' => 'sometimes|required|numeric|min:1',
                'unit_of_measurement' => 'sometimes|required|string|max:50',
                'latitude' => 'sometimes|required|numeric',
                'longitude' => 'sometimes|required|numeric',
                'address_description' => 'sometimes|required|string',
                'status' => 'sometimes|required|string|in:disponible,reservado,entregado,expirado',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }

            $offer->update($validator->validated());
            
            return response()->json([
                'success' => true,
                'message' => 'Oferta actualizada por administrador',
                'data' => $offer
            ]);
        }

        // Coordinator puede editar: título, descripción, categoría, cantidad, estado, coordenadas
        if ($isCoordinator) {
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'title' => 'sometimes|required|string|max:200',
                'description' => 'sometimes|required|string',
                'category' => 'sometimes|required|string|in:comida,ropa,vivienda,salud,transporte,otros',
                'quantity' => 'sometimes|required|numeric|min:1',
                'unit_of_measurement' => 'sometimes|required|string|max:50',
                'latitude' => 'sometimes|required|numeric',
                'longitude' => 'sometimes|required|numeric',
                'address_description' => 'sometimes|required|string',
                'status' => 'sometimes|required|string|in:disponible,reservado,entregado,expirado',
                // Coordinator NO puede cambiar estos campos
                'provider_name' => 'prohibited',
                'dni' => 'prohibited',
                'phone' => 'prohibited',
            ], [
                'provider_name.prohibited' => 'El nombre del proveedor no puede ser modificado.',
                'dni.prohibited' => 'El DNI no puede ser modificado.',
                'phone.prohibited' => 'El teléfono no puede ser modificado.',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }

            $offer->update($validator->validated());
            
            return response()->json([
                'success' => true,
                'message' => 'Oferta actualizada por coordinador',
                'data' => $offer
            ]);
        }

        // Usuarios normales necesitan secure_token para editar sus propias ofertas
        if ($request->header('X-Secure-Token') !== $offer->secure_token) {
            return response()->json(['success' => false, 'message' => 'Token de seguridad inválido'], 403);
        }

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:200',
            'description' => 'sometimes|required|string',
            'category' => 'sometimes|required|string|in:comida,ropa,vivienda,salud,transporte,otros',
            'quantity' => 'sometimes|required|numeric|min:1',
            'unit_of_measurement' => 'sometimes|required|string|max:50',
            'latitude' => 'sometimes|required|numeric',
            'longitude' => 'sometimes|required|numeric',
            'address_description' => 'sometimes|required|string',
            'status' => 'sometimes|required|string|in:disponible,reservado,entregado,expirado',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $offer->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Oferta actualizada exitosamente',
            'data' => $offer
        ]);
    }

    public function destroy($id)
    {
        $offer = AidOffer::find($id);
        if (!$offer) {
            return response()->json(['success' => false, 'message' => 'Oferta no encontrada'], 404);
        }

        $user = auth()->user();

        // Solo superadmin puede eliminar sin token
        if ($user && $user->role === 'superadmin') {
            $offer->delete();
            return response()->json([
                'success' => true,
                'message' => 'Oferta eliminada por administrador'
            ]);
        }

        // Otros usuarios necesitan secure_token
        if (request()->header('X-Secure-Token') !== $offer->secure_token) {
            return response()->json(['success' => false, 'message' => 'Token de seguridad inválido'], 403);
        }

        $offer->delete();

        return response()->json([
            'success' => true,
            'message' => 'Oferta eliminada exitosamente'
        ]);
    }
}
