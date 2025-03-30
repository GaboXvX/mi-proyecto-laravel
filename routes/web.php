<?php

use App\Http\Controllers\configController;
use App\Http\Controllers\direccionController;
use App\Http\Controllers\PersonaController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\IncidenciaController;
use App\Http\Controllers\LiderController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\movimientoController;
use App\Http\Controllers\PeticionController;
use App\Http\Controllers\recuperarController;
use App\Http\Controllers\seguridadController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RecuperarGetController;
use App\Http\Controllers\UsuarioValidacionController;
use App\Models\Direccion;
use Illuminate\Support\Facades\Route;
Route::group(['middleware' => 'prevent-back-history'],function(){
    Route::get('/validar-usuario', [UsuarioValidacionController::class, 'validarUsuario']);

Route::get('/buscar-empleado', [PeticionController::class, 'buscarEmpleado'])->name('buscar.empleado');
Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/', [LoginController::class, 'authenticate'])->name('login.authenticate');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/login', [LoginController::class, 'index'])->middleware('guest');
Route::get('registrar', [UserController::class, 'create'])->name('usuarios.create')->middleware('guest');
Route::post('aceptar/{id}', [PeticionController::class, 'aceptar'])->name('peticion.aceptar');
Route::resource('peticiones', PeticionController::class)->except('index');
 // Rutas de recuperación de contraseña
 Route::get('/recuperar-contraseña', [RecuperarController::class, 'ingresarCedula'])->name('recuperar.ingresarCedula');
 Route::post('/recuperar-contraseña/preguntas', [RecuperarController::class, 'procesarFormulario'])->name('recuperar.preguntas');
 Route::get('/recuperar-contraseña/redirigir', [RecuperarGetController::class, 'redirigirRecuperarClave'])->name('recuperar.redirigirRecuperarClave');
 // Ruta para validar la respuesta de seguridad (POST)
Route::post('/recuperar/validar-respuesta', [RecuperarController::class, 'validarRespuesta'])->name('recuperar.validarRespuesta');

// Ruta para mostrar el cambio de contraseña (POST)
Route::post('/recuperar-clave', [RecuperarController::class, 'mostrarCambioClave'])->name('recuperar.recuperarClave');

 Route::post('/cambiar-clave', [RecuperarController::class, 'mostrarCambioClave'])->name('cambiar-clave');
 Route::post('/cambiar-clave/{usuarioId}', [RecuperarController::class, 'update'])->name('cambiar.update');
Route::get('/cambiar-clave/{token}', [RecuperarController::class, 'mostrarCambioClave'])->name('cambiar-clave');
Route::post('/validar-campo-asincrono', [PeticionController::class, 'validarCampoAsincrono'])->name('validar.campo.asincrono');
Route::get('/peticiones/obtener', [PeticionController::class, 'obtenerPeticiones'])->name('peticiones.obtener');
Route::get('/home/total-peticiones', [HomeController::class, 'obtenerTotalPeticiones'])->name('home.totalPeticiones');
Route::middleware(['auth'])->group(function () {


    Route::get('/home', [HomeController::class, 'index'])->name('home');

    Route::get('peticiones', [PeticionController::class, 'index'])->name('peticiones.index')->middleware('role:admin');
    Route::post('/peticion/{id}', [PeticionController::class, 'rechazar'])->name('peticiones.rechazar')->middleware('role:admin');


    Route::resource('personas', PersonaController::class)->parameters(['personas' => 'slug']);

    Route::Post('/personas/buscar', [PersonaController::class, 'buscar'])->name('personas.buscar');
    Route::get('/persona/{slug}', [PersonaController::class, 'show'])->name('personas.show');
    Route::resource('usuarios', UserController::class)->except('create', 'store', 'update')->middleware('role:admin');
    Route::get('/api/usuarios', [UserController::class, 'getUsuarios'])->name('usuarios.getUsuarios');
    Route::post('/desactivar/{id}', [UserController::class, 'desactivar'])->name('usuarios.desactivar')->middleware('role:admin');
    Route::post('/activar/{id}', [UserController::class, 'activar'])->name('usuarios.activar')->middleware('role:admin');
    Route::get('/incidencias', [IncidenciaController::class, 'index']);
    Route::get('/gestionar-incidencias', [IncidenciaController::class, 'gestionar'])->name('incidencias.gestionar')->middleware('role:admin');
    Route::resource('incidencias', IncidenciaController::class)->except(['show', 'create', 'edit'])->parameters(['incidencias' => 'slug']);
    Route::get('/incidencias/{slug}/edit/{persona_slug?}', [IncidenciaController::class, 'edit'])->name('incidencias.edit');
    Route::post('/filtrar-incidencia', [IncidenciaController::class, 'filtrar'])->name('filtrar.incidencia');
    Route::post('/incidencias/{slug}/atender', [IncidenciaController::class, 'atender'])->name('incidencias.atender');
    Route::post('/incidencias/download', [IncidenciaController::class, 'download'])->name('incidencias.download');
    Route::get('/incidencias/{slug}/download', [IncidenciaController::class, 'descargar'])->name('incidencias.descargar');
    Route::post('/filtrar-incidencias-por-fechas', [IncidenciaController::class, 'filtrarPorFechas'])->name('filtrar.incidencias.fechas');
    Route::get('/persona/{slug}/incidencias/create', [IncidenciaController::class, 'crear'])->name('incidencias.crear');
    Route::get('persona/{slug}/incidencia/{incidencia_slug}', [IncidenciaController::class, 'show'])->name('incidencias.show');
    Route::get('personas/agregardirecion/{slug}', [direccionController::class, 'index'])->name('personas.agregarDireccion');
    Route::post('personas/guardardireccion/{id}', [direccionController::class, 'store'])->name('guardarDireccion');
    Route::get('/personas/modificardireccion/{slug}',[direccionController::class,'edit'])->name('personas.modificarDireccion');
    Route::post('/personas/actualizardireccion/{id}/{idPersona}',[direccionController::class,'update'])->name('personas.actualizarDireccion');
    Route::post('/personas/marcarprincipal', [direccionController::class, 'marcarPrincipal'])->name('personas.marcarPrincipal');
    Route::get('/incidencias/chart', [IncidenciaController::class, 'showChart'])->name('estadisticas')->middleware('role:admin');

    Route::get('/incidencias/{slug}', [IncidenciaController::class, 'mostrar'])->name('incidencias.mostrar');
    Route::post('/incidencias/buscar', [IncidenciaController::class, 'buscar'])->name('incidencias.buscar');
    Route::resource('lideres', liderController::class)->except('update','create');
    Route::put('/lideres/update/{slug}', [liderController::class, 'update'])->name('lideres.update');
    Route::post('/lideres/buscar', [liderController::class, 'buscar'])->name('lideres.buscar');
    Route::get('/registrarincidenciaslider/{slug}', [IncidenciaController::class, 'create'])->name('incidenciaslider.create');
    Route::get('/modificarincidencialider/{slug}', [IncidenciaController::class, 'edit'])->name('incidenciaslider.edit');

    Route::get('/configuracion', [configController::class, 'index'])->name('usuarios.configuracion');
    Route::post('/usuarios/cambiar/{id_usuario}', [UserController::class, 'update'])->name('usuarios.cambiar');
    Route::post('/usuarios/restaurar/{id_usuario}', [configController::class, 'restaurar'])->name('usuarios.restaurar');
    Route::post('/check-lider-status', [direccionController::class, 'checkLiderStatus']);
});
 });