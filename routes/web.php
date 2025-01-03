<?php

use App\Http\Controllers\PersonaController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\IncidenciaController;
use App\Http\Controllers\LiderController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\movimientoController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\PeticionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/', [LoginController::class, 'authenticate'])->name('login.authenticate');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('registrar', [UserController::class, 'create'])->name('usuarios.create')->middleware('guest');
Route::post('registrar/{id}', [UserController::class, 'store'])->name('usuarios.store');
Route::resource('peticiones', PeticionController::class)->except('index');

Route::middleware(['auth'])->group(function () {


    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // Rutas para las peticiones
    Route::get('peticiones', [PeticionController::class, 'index'])->name('peticiones.index')->middleware('role:admin');
    Route::post('/peticion/{id}', [PeticionController::class, 'rechazar'])->name('peticiones.rechazar')->middleware('role:admin');


    // Rutas para las personas
    Route::resource('personas', PersonaController::class)->parameters(['personas' => 'slug']);
    Route::Post('/personas/buscar', [PersonaController::class, 'buscar'])->name('personas.buscar');
    Route::get('/persona/{slug}', [PersonaController::class, 'show'])->name('personas.show');
    // Rutas para la gestión de usuarios (solo admin)
    Route::resource('usuarios', UserController::class)->except('create', 'store')->middleware('role:admin');
    Route::post('/desactivar/{id}', [UserController::class, 'desactivar'])->name('usuarios.desactivar')->middleware('role:admin');
    Route::post('/activar/{id}', [UserController::class, 'activar'])->name('usuarios.activar')->middleware('role:admin');
    Route::get('/gestionar-incidencias', [IncidenciaController::class, 'index'])->name('incidencias.gestionar')->middleware('role:admin');
    // Rutas para las incidencias
    Route::resource('incidencias', IncidenciaController::class)->except(['show', 'create', 'edit'])->parameters(['incidencias' => 'slug']);  // Usamos slug
    Route::get('/incidencias/{slug}/edit/{persona_slug?}', [IncidenciaController::class, 'edit'])->name('incidencias.edit');
    Route::post('/filtrar-incidencia', [IncidenciaController::class, 'filtrar'])->name('filtrar.incidencia');
    Route::post('/incidencias/{slug}/atender', [IncidenciaController::class, 'atender'])->name('incidencias.atender');
    Route::get('/incidencias/{slug}/download', [IncidenciaController::class, 'download'])->name('incidencias.download');

    // Rutas de creación de incidencias para una persona o un líder
    Route::get('/persona/{slug}/incidencias/create', [IncidenciaController::class, 'create'])->name('incidencias.create');
    Route::get('/persona/{persona_slug}/incidencia/{incidencia_slug}', [IncidenciaController::class, 'show'])
        ->name('incidencias.show');

    // routes/web.php
    Route::get('/incidencias/chart', [IncidenciaController::class, 'showChart'])->name('estadisticas');

    Route::get('/incidencias/{slug}', [IncidenciaController::class, 'mostrar'])->name('incidencias.mostrar');

    // Rutas para gestionar los líderes
    Route::resource('lideres', LiderController::class);
    Route::Post('/lideres/buscar', [LiderController::class, 'buscar'])->name('lideres.buscar');

    // Rutas específicas para la creación y modificación de incidencias de líderes
    Route::get('/registrarincidenciaslider/{slug}', [IncidenciaController::class, 'create'])->name('incidenciaslider.create');
    Route::get('/modificarincidencialider/{slug}', [IncidenciaController::class, 'edit'])->name('incidenciaslider.edit');
    Route::post('/pdf/generar', [PdfController::class, 'generar'])->name('pdf.generar');
    Route::get('/movimientos', [movimientoController::class, 'index'])->name('movimientos');
});
