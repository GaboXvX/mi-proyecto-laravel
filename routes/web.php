<?php

use App\Http\Controllers\configController;
use App\Http\Controllers\direccionController;
use App\Http\Controllers\DomicilioController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\IncidenciaController;
use App\Http\Controllers\LiderController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\movimientoController;
use App\Http\Controllers\NotificacionController;
use App\Http\Controllers\PersonaController;
use App\Http\Controllers\PeticionController;
use App\Http\Controllers\RecuperarController;
use App\Http\Controllers\RecuperarGetController;
use App\Http\Controllers\RenovacionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'prevent-back-history'], function () {
    // routes/web.php
    Route::controller(RenovacionController::class)->group(function () {
        Route::get('/renovar-solicitud', 'mostrarFormulario')->name('renovacion.mostrar');
        Route::post('/renovar-solicitud', 'procesarFormulario')->name('renovacion.procesar');
    });

    Route::get('/buscar-empleado', [PeticionController::class, 'buscarEmpleado'])->name('buscar.empleado');

    // Rutas de autenticación
    Route::middleware('guest')->group(function () {
        Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
        Route::post('/', [LoginController::class, 'authenticate'])->name('login.authenticate');
        Route::get('/login', [LoginController::class, 'index'])->name('login');
        Route::get('registrar', [UserController::class, 'create'])->name('usuarios.create');

        // Rutas de recuperación de contraseña
        Route::controller(RecuperarController::class)->group(function () {
            Route::get('/recuperar-contraseña', 'ingresarCedula')->name('recuperar.ingresarCedula');
            Route::post('/recuperar-contraseña/preguntas', 'procesarFormulario')->name('recuperar.preguntas');
            Route::post('/recuperar/validar-respuesta', 'validarRespuesta')->name('recuperar.validarRespuesta');
            Route::post('/cambiar-email/{usuarioId}', 'actualizarCorreo')->name('cambiar.email');
            Route::post('/recuperar-clave', 'mostrarCambioClave')->name('recuperar.recuperarClave');
            Route::get('/cambiar-clave/{token}', 'mostrarCambioClave')->name('cambiar-clave');
            Route::post('/cambiar-clave', 'mostrarCambioClave')->name('cambiar-clave');
            Route::post('/cambiar-clave/{usuarioId}', 'update')->name('cambiar.update');
        });

        Route::get('/recuperar-contraseña/redirigir', [RecuperarGetController::class, 'redirigirRecuperarClave'])->name('recuperar.redirigirRecuperarClave');
    });
    Route::post('peticiones', [PeticionController::class,'store'])->name('peticiones.store');

    Route::middleware(['auth'])->group(function () {
        Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
        Route::get('/home', [HomeController::class, 'index'])->name('home');
        Route::get('/home/total-peticiones', [HomeController::class, 'obtenerTotalPeticiones'])->name('home.totalPeticiones');

        // Rutas de peticiones
        Route::controller(PeticionController::class)->group(function () {
            Route::get('peticiones', 'index')->name('peticiones.index');
            Route::post('aceptar/{id}', 'aceptar')->name('peticion.aceptar');
            Route::post('/peticion/{id}', 'rechazar')->name('peticiones.rechazar');
            Route::get('/peticiones/obtener', 'obtenerPeticiones')->name('peticiones.obtener');
            Route::post('/validar-campo-asincrono', 'validarCampoAsincrono')->name('validar.campo.asincrono');
        });

        // Rutas de personas
        Route::controller(PersonaController::class)->group(function () {
            Route::get('/personas/registrar', 'create')->name('personas.create');
            Route::post('/personas', 'store')->name('personas.store');
            Route::post('/personas/validar-cedula', 'validarCedula')->name('personas.validar-cedula');
            Route::post('/personas/validar-correo', 'validarCorreo')->name('personas.validar-correo');
            Route::Post('/personas/buscar', 'buscar')->name('personas.buscar');
            Route::get('/persona/{slug}', 'show')->name('personas.show');
            Route::get('/personas/{slug}/incidencias', 'verIncidencias')->name('personas.incidencias');
            Route::get('/api/personas/{id}/direcciones', 'obtenerDirecciones');
        });
        Route::resource('personas', PersonaController::class)->parameters(['personas' => 'slug'])->except(['create', 'store', 'destroy']);

        // Rutas de usuarios
        Route::controller(UserController::class)->middleware('can:ver empleados')->group(function () {
            Route::get('/usuarios/{slug}/movimientos', 'movimientos')->name('usuarios.movimientos');
            Route::post('/desactivar/{id}', 'desactivar')->name('usuarios.desactivar')->middleware('can:desactivar empleados');
            Route::post('/activar/{id}', 'activar')->name('usuarios.activar')->middleware('can:habilitar empleados');
            Route::post('/usuarios/{id}/asignar-permiso', 'asignarPermiso')->name('usuarios.asignarPermiso');
            Route::post('/usuarios/{usuario}/toggle-permiso', 'togglePermiso')->name('usuarios.togglePermiso');
            Route::get('/usuarios/{id_usuario}/asignar-permisos', 'asignarPermisosVista')->name('usuarios.asignarPermisos');
            Route::post('/usuarios/toggle-permiso-ajax', 'togglePermisoAjax')->name('usuarios.togglePermisoAjax');
            Route::post('/usuarios/cambiar/{id_usuario}', 'update')->name('usuarios.cambiar');
        });
        Route::resource('usuarios', UserController::class)->except(['create', 'store', 'update', 'destroy']);
        Route::get('/validar-usuario/{nombre_usuario}/{excluir?}', [UserController::class, 'validarUsuario']);
        Route::get('/validar-correo/{email}/{excluir?}', [UserController::class, 'validarCorreo']);
        Route::get('/mis-movimientos', function () {
            return redirect()->route('usuarios.movimientos', auth()->user()->slug);
        })->name('mis.movimientos');

        // Rutas de incidencias
        Route::controller(IncidenciaController::class)->group(function () {
            Route::get('/incidencias', 'index');
            Route::get('/incidencias/{slug}/edit/{persona_slug?}', 'edit')->name('incidencias.edit')->middleware('can:cambiar estado de incidencias');
            Route::post('/filtrar-incidencia', 'filtrar')->name('filtrar.incidencia');
            Route::post('/incidencias/{slug}/atender', 'atender')->name('incidencias.atender');
            Route::post('/incidencias/download', 'download')->name('incidencias.download');
            Route::get('/incidencias/descargar/{slug}', 'descargar')->name('incidencias.descargar')->middleware('can:descargar grafica incidencia');
            Route::post('/filtrar-incidencias-por-fechas', 'filtrarPorFechas')->name('filtrar.incidencias.fechas');
            Route::post('/incidencias/generar-pdf', 'generarPDF')->name('incidencias.generarPDF')->middleware('can:descargar listado incidencias');
            Route::get('/incidencias/chart', 'showChart')->name('estadisticas')->middleware('can:ver grafica incidencia');
            Route::get('/persona/{slug}/incidencias/create', 'crear')->name('incidencias.crear');
            // Route::get('persona/{slug}/incidencia/{incidencia_slug}', 'show')->name('incidencias.show');
            Route::post('/incidencias/buscar', 'buscar')->name('incidencias.buscar');
            Route::get('/incidencias/{slug}/atender', [IncidenciaController::class, 'atenderVista'])->name('incidencias.atender.vista');
            Route::post('/incidencias/{slug}/atender', [IncidenciaController::class, 'atenderGuardar'])->name('incidencias.atender.guardar');
            Route::get('/incidencias/{slug}/ver', [IncidenciaController::class, 'ver'])->name('incidencias.ver');
        });
        Route::resource('incidencias', IncidenciaController::class)->except(['show', 'create', 'edit', 'destroy'])->parameters(['incidencias' => 'slug']);
        Route::get('/instituciones-estaciones/direccion/{direccion}', [IncidenciaController::class, 'getInstitucionesEstacionesPorDireccion']);
        Route::get('/instituciones-estaciones/municipio/{municipio}', [IncidenciaController::class, 'getEstacionesPorMunicipio']);
        Route::get('/instituciones-estaciones/estado/{estado}/institucion/{institucion}', [IncidenciaController::class, 'getEstacionesPorEstadoEInstitucion']);
        Route::get('/incidencias-generales/create', [IncidenciaController::class, 'create'])->name('incidencias.create');
        Route::get('incidencias/{slug}/edit', [IncidenciaController::class, 'edit'])->name('incidencias.edit');
        Route::get('incidencia/{slug}', [IncidenciaController::class, 'show'])->name('incidencias.show');

        // Rutas de direcciones
        Route::controller(DomicilioController::class)->group(function () {
            Route::get('personas/agregardomicilio/{slug}', 'index')->name('personas.agregarDireccion');
            Route::post('personas/guardardomicilio/{id}', 'store')->name('guardarDireccion');
            Route::get('/personas/modificardomicilio/{slug}', 'edit')->name('personas.modificarDireccion');
            Route::post('/personas/actualizardomicilio/{id}/{idPersona}', 'update')->name('personas.actualizarDireccion');
            Route::post('/personas/marcarprincipal', 'marcarPrincipal')->name('personas.marcarPrincipal');
            Route::post('/check-lider-status', 'checkLiderStatus');
        });

        // Rutas de configuración
        Route::controller(configController::class)->group(function () {
            Route::get('/configuracion', 'index')->name('usuarios.configuracion');
            Route::post('/usuarios/{usuario}/cambiar', 'actualizar')->name('usuarios.cambiar');
            Route::post('/usuarios/{usuario}/cambiar-preguntas', 'cambiarPreguntas')->name('usuarios.cambiar-preguntas');
            Route::post('/usuarios/restaurar/{id_usuario}', 'restaurar')->name('usuarios.restaurar')->middleware('can:restaurar usuarios');
        });

        // Rutas de notificaciones
        Route::prefix('notificaciones')->group(function () {
            Route::get('/', [NotificacionController::class, 'index'])->name('notificaciones.index');
            Route::get('/marcar-leida/{id}', [NotificacionController::class, 'marcarComoLeida'])->name('notificaciones.marcar-leida');
            Route::post('/marcar-todas-leidas', [NotificacionController::class, 'marcarTodasComoLeidas'])->name('notificaciones.marcar-todas-leidas');
            Route::get('/contador', [NotificacionController::class, 'getContadorNoLeidas'])->name('notificaciones.contador');
        });

        // Rutas de movimientos
        Route::controller(movimientoController::class)->group(function () {
            Route::get('/mis-movimientos', 'index')->name('mis.movimientos');
            Route::get('/mis-movimientos/exportar', 'exportar')->name('movimientos.exportar');
            Route::get('/mis-movimientos/descargar/{id}', 'descargar')->name('movimientos.descargar');
        });

        // Rutas de movimientos por usuario (individual)
        Route::get('/usuarios/{slug}/movimientos', [movimientoController::class, 'movimientosPorUsuario'])->name('movimientos.registradores');
    });
});