<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\AidOfferController;
use App\Http\Controllers\Api\V1\CollectionCenterController;
use App\Http\Controllers\Api\V1\IncidentController;
use App\Http\Controllers\Api\V1\CountryAidController;

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
    Route::post('/hospitals', [App\Http\Controllers\Api\V1\HospitalController::class, 'store']);
    Route::get('/hospitals', [App\Http\Controllers\Api\V1\HospitalController::class, 'index']);

    Route::get('/patients/search', [App\Http\Controllers\Api\V1\PatientController::class, 'search']);
    Route::post('/patients', [App\Http\Controllers\Api\V1\PatientController::class, 'store']);
    
    // Dashboard Stats
    Route::get('/dashboard/stats', [App\Http\Controllers\Api\V1\DashboardController::class, 'stats']);

    // Mascotas Perdidas
    Route::post('/lost-pets', [App\Http\Controllers\Api\V1\LostPetController::class, 'store']);
    Route::get('/lost-pets', [App\Http\Controllers\Api\V1\LostPetController::class, 'index']);
    Route::get('/lost-pets/{id}', [App\Http\Controllers\Api\V1\LostPetController::class, 'show']);

    // Zonas Afectadas
    Route::post('/affected-zones', [App\Http\Controllers\Api\V1\AffectedZoneController::class, 'store']);
    Route::get('/affected-zones', [App\Http\Controllers\Api\V1\AffectedZoneController::class, 'index']);
    Route::get('/affected-zones/{id}', [App\Http\Controllers\Api\V1\AffectedZoneController::class, 'show']);

    // Voluntarios
    Route::post('/volunteers', [App\Http\Controllers\Api\V1\VolunteerController::class, 'store']);
    Route::get('/volunteers', [App\Http\Controllers\Api\V1\VolunteerController::class, 'index']);
    Route::get('/volunteers/{id}', [App\Http\Controllers\Api\V1\VolunteerController::class, 'show']);

    // Necesidades
    Route::post('/needs', [App\Http\Controllers\Api\V1\NeedController::class, 'store']);
    Route::get('/needs', [App\Http\Controllers\Api\V1\NeedController::class, 'index']);
    Route::patch('/needs/{id}/status', [App\Http\Controllers\Api\V1\NeedController::class, 'updateStatus']);
    Route::post('/needs/{id}/responses', [App\Http\Controllers\Api\V1\NeedController::class, 'storeResponse']);
    Route::get('/needs/{id}/responses', [App\Http\Controllers\Api\V1\NeedController::class, 'getResponses']);

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
        // AidOffers - Admin/Coordinator tienen acceso total, usuarios normales con secure_token
        Route::put('/aid-offers/{id}', [AidOfferController::class, 'update']);
        Route::delete('/aid-offers/{id}', [AidOfferController::class, 'destroy']);
        
        // CollectionCenters - Admin/Coordinator tienen acceso total, usuarios normales con secure_token
        Route::put('/collection-centers/{id}', [CollectionCenterController::class, 'update']);
        Route::delete('/collection-centers/{id}', [CollectionCenterController::class, 'destroy']);

        // LostPets
        Route::put('/lost-pets/{id}', [App\Http\Controllers\Api\V1\LostPetController::class, 'update']);
        Route::delete('/lost-pets/{id}', [App\Http\Controllers\Api\V1\LostPetController::class, 'destroy']);

        // AffectedZones
        Route::put('/affected-zones/{id}', [App\Http\Controllers\Api\V1\AffectedZoneController::class, 'update']);
        Route::delete('/affected-zones/{id}', [App\Http\Controllers\Api\V1\AffectedZoneController::class, 'destroy']);
        
        // Voluntarios
        Route::put('/volunteers/{id}', [App\Http\Controllers\Api\V1\VolunteerController::class, 'update']);

        // Pacientes
        Route::post('/patients/{id}', [App\Http\Controllers\Api\V1\PatientController::class, 'update']);
        Route::delete('/patients/{id}', [App\Http\Controllers\Api\V1\PatientController::class, 'destroy']);
    });
});
