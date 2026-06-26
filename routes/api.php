<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\AidOfferController;
use App\Http\Controllers\Api\V1\CollectionCenterController;
use App\Http\Controllers\Api\V1\IncidentController;
use App\Http\Controllers\Api\V1\CountryAidController;

Route::prefix('v1')->group(function () {
    // Fase I: Endpoints Públicos de Emergencia (Creación y Lectura)
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

    // Endpoints que requieren 'secure_token' (Modificación/Eliminación)
    Route::middleware('App\Http\Middleware\CheckSecureToken')->group(function () {
        Route::put('/aid-offers/{id}', [AidOfferController::class, 'update']);
        Route::delete('/aid-offers/{id}', [AidOfferController::class, 'destroy']);
        
        Route::put('/collection-centers/{id}', [CollectionCenterController::class, 'update']);
        Route::delete('/collection-centers/{id}', [CollectionCenterController::class, 'destroy']);
    });

    // Fase II: Autenticación (JWT)
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
    });
});
