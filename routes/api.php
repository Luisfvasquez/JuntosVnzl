<?php

use App\Http\Controllers\Api\V1\AffectedZoneController;
use App\Http\Controllers\Api\V1\AffectedZoneUpdateController;
use App\Http\Controllers\Api\V1\AidOfferController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CollectionCenterController;
use App\Http\Controllers\Api\V1\CountryAidController;
use App\Http\Controllers\Api\V1\DamagedBuildingController;
use App\Http\Controllers\Api\V1\DamagedBuildingEvaluationController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\EvacueeController;
use App\Http\Controllers\Api\V1\HospitalController;
use App\Http\Controllers\Api\V1\IncidentController;
use App\Http\Controllers\Api\V1\LostPetController;
use App\Http\Controllers\Api\V1\NeedController;
use App\Http\Controllers\Api\V1\PatientController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\VolunteerController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // ============================================
    // FASE I: ENDPOINTS PÚBLICOS DE EMERGENCIA
    // ============================================
    Route::post('/aid-offers', [AidOfferController::class, 'store']);
    Route::get('/aid-offers', [AidOfferController::class, 'index']);
    Route::get('/aid-offers/{id}', [AidOfferController::class, 'show']);

    Route::post('/collection-centers', [CollectionCenterController::class, 'store']);
    Route::get('/collection-centers', [CollectionCenterController::class, 'index']);
    Route::get('/collection-centers/{id}', [CollectionCenterController::class, 'show']);

    Route::post('/incidents', [IncidentController::class, 'store']);
    Route::get('/incidents', [IncidentController::class, 'index']);
    Route::get('/incidents/{id}', [IncidentController::class, 'show']);

    Route::post('/international-aid', [CountryAidController::class, 'store']);
    Route::get('/international-aid', [CountryAidController::class, 'index']);

    // Módulo de Salud (Hospitales y Pacientes)
    Route::post('/hospitals', [HospitalController::class, 'store']);
    Route::get('/hospitals', [HospitalController::class, 'index']);

    Route::get('/patients/search', [PatientController::class, 'search']);
    Route::post('/patients', [PatientController::class, 'store']);

    // Dashboard Stats
    Route::get('/dashboard/stats', [DashboardController::class, 'stats']);

    // Mascotas Perdidas
    Route::post('/lost-pets', [LostPetController::class, 'store']);
    Route::get('/lost-pets', [LostPetController::class, 'index']);
    Route::get('/lost-pets/{id}', [LostPetController::class, 'show']);

    // Damnificados (Evacuees)
    Route::get('/evacuees/search', [EvacueeController::class, 'index']);
    Route::get('/evacuees/{id}', [EvacueeController::class, 'show']);
    Route::post('/evacuees', [EvacueeController::class, 'store']);
    Route::put('/evacuees/{id}', [EvacueeController::class, 'update']);
    Route::delete('/evacuees/{id}', [EvacueeController::class, 'destroy']);

    // Zonas Afectadas
    Route::post('/affected-zones', [AffectedZoneController::class, 'store']);
    Route::get('/affected-zones', [AffectedZoneController::class, 'index']);
    Route::get('/affected-zones/{id}', [AffectedZoneController::class, 'show']);
    Route::put('/affected-zones/{id}', [AffectedZoneController::class, 'update']);
    Route::delete('/affected-zones/{id}', [AffectedZoneController::class, 'destroy']);
    Route::get('/affected-zones/{id}/updates', [AffectedZoneUpdateController::class, 'index']);
    Route::post('/affected-zones/{id}/updates', [AffectedZoneUpdateController::class, 'store']);

    // Edificios Dañados
    Route::post('/damaged-buildings', [DamagedBuildingController::class, 'store']);
    Route::get('/damaged-buildings', [DamagedBuildingController::class, 'index']);
    Route::get('/damaged-buildings/{id}', [DamagedBuildingController::class, 'show']);
    Route::get('/damaged-buildings/{id}/evaluations', [DamagedBuildingEvaluationController::class, 'index']);
    Route::post('/damaged-buildings/{id}/evaluations', [DamagedBuildingEvaluationController::class, 'store']);

    // Voluntarios
    Route::post('/volunteers', [VolunteerController::class, 'store']);
    Route::get('/volunteers', [VolunteerController::class, 'index']);
    Route::get('/volunteers/{id}', [VolunteerController::class, 'show']);

    // Necesidades
    Route::post('/needs', [NeedController::class, 'store']);
    Route::get('/needs', [NeedController::class, 'index']);
    Route::patch('/needs/{id}/status', [NeedController::class, 'updateStatus']);
    Route::post('/needs/{id}/responses', [NeedController::class, 'storeResponse']);
    Route::get('/needs/{id}/responses', [NeedController::class, 'getResponses']);

    // ============================================
    // AUTENTICACIÓN (JWT)
    // ============================================
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
    });

    // ============================================
    // ENDPOINTS PROTEGIDOS POR JWT
    // Admin y Coordinator pueden editar/eliminar sin secure_token
    // Usuarios normales siguen usando secure_token
    // ============================================
    Route::middleware('auth:api')->group(function () {
        // Usuarios (Solo Administradores)
        Route::get('/users', [App\Http\Controllers\Api\V1\UserController::class, 'index']);
        Route::get('/users/{id}', [App\Http\Controllers\Api\V1\UserController::class, 'show']);
        Route::put('/users/{id}', [App\Http\Controllers\Api\V1\UserController::class, 'update']);
        Route::delete('/users/{id}', [App\Http\Controllers\Api\V1\UserController::class, 'destroy']);

        // AidOffers - Admin/Coordinator tienen acceso total, usuarios normales con secure_token
        Route::put('/aid-offers/{id}', [AidOfferController::class, 'update']);
        Route::delete('/aid-offers/{id}', [AidOfferController::class, 'destroy']);

        // CollectionCenters - Admin/Coordinator tienen acceso total, usuarios normales con secure_token
        Route::put('/collection-centers/{id}', [CollectionCenterController::class, 'update']);
        Route::delete('/collection-centers/{id}', [CollectionCenterController::class, 'destroy']);

        // LostPets
        Route::put('/lost-pets/{id}', [LostPetController::class, 'update']);
        Route::delete('/lost-pets/{id}', [LostPetController::class, 'destroy']);

        // AffectedZones
        Route::delete('/affected-zones/updates/{id}', [AffectedZoneUpdateController::class, 'destroy']);

        Route::put('/volunteers/{id}', [VolunteerController::class, 'update']);
        
        // Edificios Dañados
        Route::put('/damaged-buildings/{id}', [DamagedBuildingController::class, 'update']);
        Route::delete('/damaged-buildings/{id}', [DamagedBuildingController::class, 'destroy']);
        Route::put('/damaged-buildings/evaluations/{id}', [DamagedBuildingEvaluationController::class, 'update']);
        Route::delete('/damaged-buildings/evaluations/{id}', [DamagedBuildingEvaluationController::class, 'destroy']);

        // Pacientes
        Route::put('/patients/{id}', [PatientController::class, 'update']);
        Route::delete('/patients/{id}', [PatientController::class, 'destroy']);
    });
});
