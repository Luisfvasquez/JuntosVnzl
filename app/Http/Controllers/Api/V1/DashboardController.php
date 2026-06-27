<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AidOffer;
use App\Models\CollectionCenter;
use App\Models\Incident;
use App\Models\Need;
use App\Models\Patient;

class DashboardController extends Controller
{
    public function stats(Request $request)
    {
        $totalOffers = AidOffer::count();
        $totalNeeds = Need::count();
        $totalPatients = Patient::count();
        $totalCenters = CollectionCenter::count();
        $totalIncidents = Incident::count();

        // Calculate critical incidents and active centers
        $criticalIncidents = Incident::whereIn('severity', ['critico', 'catastrofico'])->count();
        $activeCenters = CollectionCenter::whereIn('status', ['recibiendo_insumos', 'distribuyendo'])->count();

        return response()->json([
            'success' => true,
            'message' => 'Estadísticas del dashboard',
            'data' => [
                'offers' => [
                    'total' => $totalOffers,
                    'total_units' => 0
                ],
                'needs' => [
                    'total' => $totalNeeds
                ],
                'patients' => [
                    'total' => $totalPatients
                ],
                'centers' => [
                    'total' => $totalCenters,
                    'active' => $activeCenters
                ],
                'incidents' => [
                    'total' => $totalIncidents,
                    'critical' => $criticalIncidents
                ]
            ]
        ]);
    }
}
