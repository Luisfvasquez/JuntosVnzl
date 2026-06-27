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

    public function update(UpdateAidOfferRequest $request, $id)
    {
        $offer = AidOffer::find($id);
        if (!$offer) {
            return response()->json(['success' => false, 'message' => 'Oferta no encontrada'], 404);
        }

        // El Request de actualización ya valida el secure_token
        $offer->update($request->validated());

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
