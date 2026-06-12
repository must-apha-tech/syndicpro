<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Residences\ResidenceController;
use App\Http\Controllers\Residences\LotController;
use App\Http\Controllers\Comptabilite\ExerciceController;
use App\Http\Controllers\Comptabilite\AppelDeFondsController;
use App\Http\Controllers\Paiements\PaiementController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\AG\AssemblyController;
use App\Http\Controllers\Incidents\IncidentController;
use App\Http\Controllers\Auth\RegisterWithCodeController;
use App\Http\Controllers\Residences\AccessCodeController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public Routes
Route::post('/auth/register', [RegisterController::class, 'register']);
Route::post('/auth/login', [LoginController::class, 'login']);

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [LoginController::class, 'logout']);

    // Dashboard
    Route::get('/dashboard/summary', [DashboardController::class, 'summary']);
    Route::get('/dashboard/taux-recouvrement', [DashboardController::class, 'recoveryRate']);
    Route::get('/dashboard/impayés', [DashboardController::class, 'unpaid']);

    // Residences
    Route::apiResource('residences', ResidenceController::class);
    Route::get('/residences/{residenceId}/lots', [LotController::class, 'index']);
    Route::post('/residences/{residenceId}/lots', [LotController::class, 'store']);
    Route::apiResource('lots', LotController::class)->except(['index', 'store']);

    // Accounting
    Route::get('/residences/{residenceId}/exercices', [ExerciceController::class, 'index']);
    Route::post('/exercices', [ExerciceController::class, 'store']);
    Route::patch('/exercices/{id}/close', [ExerciceController::class, 'close']);

    Route::get('/exercices/{exerciceId}/appels', [AppelDeFondsController::class, 'index']);
    Route::post('/appels', [AppelDeFondsController::class, 'store']);

    // Payments
    Route::post('/paiements', [PaiementController::class, 'store']);
    Route::get('/paiements/{id}/receipt', [PaiementController::class, 'getReceipt']);

    // General Assembly
    Route::get('/assemblies', [AssemblyController::class, 'index']);
    Route::post('/assemblies', [AssemblyController::class, 'store']);

    // Incidents
    Route::get('/incidents', [IncidentController::class, 'index']);
    Route::post('/incidents', [IncidentController::class, 'store']);
    Route::patch('/incidents/{id}/status', [IncidentController::class, 'updateStatus']);

    // Access Codes (Syndic management)
    Route::post('/residences/{residence}/lots/{lot}/access-code', [AccessCodeController::class, 'generate']);
    Route::get('/residences/{residence}/access-codes', [AccessCodeController::class, 'list']);
    Route::delete('/access-codes/{accessCode}', [AccessCodeController::class, 'revoke']);
});

// Public Access Code routes
Route::post('/auth/validate-code', [RegisterWithCodeController::class, 'validateCode']);
Route::post('/auth/register-with-code', [RegisterWithCodeController::class, 'register']);
