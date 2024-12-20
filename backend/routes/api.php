<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\FinancialTransactionController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectSituationController;
use App\Http\Controllers\ProjectTypeController;
use App\Http\Controllers\EmployeeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('projects', ProjectController::class);
Route::apiResource('clients', ClientController::class);
Route::resource('project-situations', ProjectSituationController::class);
Route::resource('project-types', ProjectTypeController::class);
Route::apiResource('financial-transactions', FinancialTransactionController::class);
Route::get('projects', [ProjectController::class, 'index']);


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::middleware('auth:sanctum')->put('/user', [AuthController::class, 'update']);
Route::middleware('auth:sanctum')->get('/projects/has', [ProjectController::class, 'hasProjects']);
Route::post('/refresh-token', [AuthController::class, 'refresh']);

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('clients', ClientController::class);

    // Rotas para gerenciar endereços de clientes
    Route::get('/clients/{clientId}/addresses', [ClientController::class, 'getClientAddresses']);
    Route::post('/clients/{clientId}/addresses', [ClientController::class, 'createAddress']);
    Route::get('/clients/addresses/{addressId}/projects', [ClientController::class, 'getAddressProjects']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('employees', EmployeeController::class);

    // Rotas para gerenciar projetos de funcionários
    Route::post('employees/{id}/projects', [EmployeeController::class, 'addProject']);
    Route::get('employees/{id}/projects', [EmployeeController::class, 'listProjects']);
    Route::put('employees/{employeeId}/projects/{projectId}', [EmployeeController::class, 'updateProject']);
    Route::delete('employees/{employeeId}/projects/{projectId}', [EmployeeController::class, 'removeProject']);
    Route::delete('/employees/{id}', [EmployeeController::class, 'destroy']);
});


Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('projects', ProjectController::class);
    
    // Rotas específicas para responsáveis por projetos
    Route::post('/projects/{id}/responsible', [ProjectController::class, 'addResponsible']);
    Route::put('/projects/{id}/responsible', [ProjectController::class, 'updateResponsible']);
    Route::delete('/projects/{id}/responsible', [ProjectController::class, 'deleteResponsible']);
    Route::get('/projects/{id}/responsible', [ProjectController::class, 'getResponsible']);    
});

Route::get('/projects/{projectId}/financial-movements', [FinancialTransactionController::class, 'getTransactionsByProject'])
    ->middleware('auth:sanctum');

    Route::middleware('auth:sanctum')->get('/projects/has/{id}', [ProjectController::class, 'hasProjects']);

    Route::middleware('auth:sanctum')->delete('/projects/{id}', [ProjectController::class, 'destroy']);