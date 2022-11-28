<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ConsultorioController;
use App\Http\Controllers\DepartamentoController;
use App\Http\Controllers\MunicipioController;
use App\Http\Controllers\PersonaController;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\DiagnosticoController;
use App\Http\Controllers\MedicamentoController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('test', function(){
    $data = ["name" => "jose", "lastname" => "diaz"];
    return response()->json($data);
});
Route::post('test', function(Request $request){
    
    return response()->json($request->all());
});
Route::post('login', [AuthController::class, 'login']);
Route::middleware(['auth:sanctum'])->group(function() {
    
    Route::post('registro', [AuthController::class, 'register']);
    Route::get('logout', [AuthController::class, 'logout']);

    Route::get('persona/{perfil}', [PersonaController::class, 'index']);
    Route::get('persona/id/{id}', [PersonaController::class, 'getPersonaPorId']);
    Route::post('persona', [PersonaController::class, 'store']);
    Route::put('persona', [PersonaController::class, 'update']);
    Route::delete('persona/{id}', [PersonaController::class, 'destroy']);

    Route::get('consultorio', [ConsultorioController::class, 'index']);
    Route::get('consultorio/{id_departamento}', [ConsultorioController::class, 'getConsultoriosDepartamento']);
    Route::post('consultorio', [ConsultorioController::class, 'store']);
    Route::put('consultorio', [ConsultorioController::class, 'update']);
    Route::delete('consultorio/{id}', [ConsultorioController::class, 'destroy']);

    Route::get('agenda/{id}', [AgendaController::class, 'index']);
    Route::post('agenda', [AgendaController::class, 'store']);
    Route::post('agenda/disponible', [AgendaController::class, 'agenda_disponible']);
    Route::put('agenda', [AgendaController::class, 'update']);
    Route::delete('agenda/{id}', [AgendaController::class, 'destroy']);

    Route::get('cita/{id_persona}', [CitaController::class, 'index']);
    Route::post('cita', [CitaController::class, 'store']);
    Route::post('cita/cancelar', [CitaController::class, 'cancelarCita']);
    Route::put('cita', [CitaController::class, 'update']);
    Route::delete('cita/{id}', [CitaController::class, 'destroy']);

    Route::get('diagnostico/{id_cita}', [DiagnosticoController::class, 'index']);
    Route::post('diagnostico', [DiagnosticoController::class, 'store']);
    Route::put('diagnostico', [DiagnosticoController::class, 'update']);
    Route::delete('diagnostico/{id}', [DiagnosticoController::class, 'destroy']);

    Route::get('medicamento', [MedicamentoController::class, 'index']);

    Route::get('departamento', [DepartamentoController::class, 'index']);
    Route::get('municipio', [MunicipioController::class, 'index']);
    Route::get('municipio/departamento/{id}', [MunicipioController::class, 'getPorDepartamento']);
});
