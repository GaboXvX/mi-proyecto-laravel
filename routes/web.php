<?php

use App\Http\Controllers\CategoriaPersonaController;
use App\Http\Controllers\configController;
use App\Http\Controllers\direccionController;
use App\Http\Controllers\DomicilioController;
use App\Http\Controllers\EmpleadoAutorizadoController;
use App\Http\Controllers\GraficoIncidenciasController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\IncidenciaController;
use App\Http\Controllers\institucionController;
use App\Http\Controllers\LiderController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\movimientoController;
use App\Http\Controllers\nivelIncidenciaController;
use App\Http\Controllers\NotificacionController;
use App\Http\Controllers\PersonaController;
use App\Http\Controllers\personalController;
use App\Http\Controllers\PeticionController;
use App\Http\Controllers\RecuperarController;
use App\Http\Controllers\RecuperarGetController;
use App\Http\Controllers\RenovacionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

Route::group(['middleware' => 'prevent-back-history'], function () {
    // Rutas públicas (sin autenticación)
    Route::controller(RenovacionController::class)->group(function () {
        Route::get('/renovar-solicitud', 'mostrarFormulario')->name('renovacion.mostrar');
        Route::post('/renovar-solicitud', 'procesarFormulario')->name('renovacion.procesar');
    });

    Route::get('/buscar-empleado', [PeticionController::class, 'buscarEmpleado'])->name('buscar.empleado');
    Route::post('peticiones', [PeticionController::class, 'store'])->name('peticiones.store');

    // Rutas de autenticación y recuperación (guest)
    Route::middleware('guest')->group(function () {
        // Login
        Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
        Route::post('/', [LoginController::class, 'authenticate'])->name('login.authenticate');
        Route::get('/login', [LoginController::class, 'index'])->name('login');
        Route::get('registrar', [UserController::class, 'create'])->name('usuarios.create');

        // Recuperación de contraseña
        Route::get('/recuperar-contraseña', [RecuperarController::class, 'ingresarCedula'])->name('recuperar.ingresarCedula');
        Route::post('/recuperar-contraseña/preguntas', [RecuperarController::class, 'procesarFormulario'])->name('recuperar.preguntas');
        Route::get('/recuperar-contraseña/redirigir', [RecuperarGetController::class, 'redirigirRecuperarClave'])->name('recuperar.redirigirRecuperarClave');
        Route::post('/recuperar/validar-respuesta', [RecuperarController::class, 'validarRespuesta'])->name('recuperar.validarRespuesta');
        Route::get('/cambiar-clave/{token}', [RecuperarController::class, 'mostrarCambioClave'])->name('cambiar-clave');
        
        // Rutas para actualizar contraseña/correo (deben estar accesibles sin auth)
        Route::post('/cambiar-clave/{usuarioId}', [RecuperarController::class, 'update'])->name('cambiar.update');
        Route::post('/cambiar-email/{usuarioId}', [RecuperarController::class, 'actualizarCorreo'])->name('cambiar.email');
    });

    // Rutas protegidas (requieren autenticación)
    Route::middleware(['auth'])->group(function () {
        // Autenticación
        Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
        
        // Home
        Route::get('/home', [HomeController::class, 'index'])->name('home');
        Route::get('/home/total-peticiones', [HomeController::class, 'obtenerTotalPeticiones'])->name('home.totalPeticiones');

        // Peticiones
        Route::controller(PeticionController::class)->group(function () {
            Route::get('peticiones', 'index')->name('peticiones.index')->middleware('can:ver peticiones');
            Route::post('aceptar/{id}', 'aceptar')->name('peticion.aceptar')->middleware('can:aceptar peticion');
            Route::post('/peticion/{id}', 'rechazar')->name('peticiones.rechazar')->middleware('can:rechazar peticiones');
            Route::get('/peticiones/obtener', 'obtenerPeticiones')->name('peticiones.obtener')->middleware('can:ver peticiones');
            Route::post('/validar-campo-asincrono', 'validarCampoAsincrono')->name('validar.campo.asincrono');
        });

        // Personas
        Route::controller(PersonaController::class)->group(function () {
            Route::get('/personas/registrar', 'create')->name('personas.create');
            Route::post('/personas', 'store')->name('personas.store');
            Route::post('/personas/validar-cedula', 'validarCedula')->name('personas.validar-cedula');
            Route::post('/personas/validar-correo', 'validarCorreo')->name('personas.validar-correo');
            Route::Post('/personas/buscar', 'buscar')->name('personas.buscar');
            Route::get('/persona/{slug}', 'show')->name('personas.show');
            Route::get('/personas/{slug}/incidencias', 'verIncidencias')->name('personas.incidencias');
            Route::get('/api/personas/{id}/direcciones', 'obtenerDirecciones');
            Route::get('/personas/download-pdf', [PersonaController::class, 'downloadPdf'])->name('personas.download.pdf');
        });
        Route::resource('personas', PersonaController::class)->parameters(['personas' => 'slug'])->except(['create', 'store', 'destroy']);

        // Usuarios
        Route::controller(UserController::class)->middleware('can:ver empleados')->group(function () {
            Route::get('/usuarios/{slug}/movimientos', 'movimientos')->name('usuarios.movimientos')->middleware('can:ver movimientos empleados');
            Route::post('/desactivar/{id}', 'desactivar')->name('usuarios.desactivar')->middleware('can:desactivar empleados');
            Route::post('/activar/{id}', 'activar')->name('usuarios.activar')->middleware('can:habilitar empleados');
            Route::post('/usuarios/{id}/asignar-permiso', 'asignarPermiso')->name('usuarios.asignarPermiso');
            Route::post('/usuarios/{usuario}/toggle-permiso', 'togglePermiso')->name('usuarios.togglePermiso');
            Route::get('/usuarios/{id_usuario}/asignar-permisos', 'asignarPermisosVista')->name('usuarios.asignarPermisos');
            Route::post('/usuarios/toggle-permiso-ajax', 'togglePermisoAjax')->name('usuarios.togglePermisoAjax');
            Route::post('/usuarios/cambiar/{id_usuario}', 'update')->name('usuarios.cambiar');
            Route::get('/usuarios/download-pdf', [UserController::class, 'downloadUsuariosPdf'])->name('usuarios.download.pdf');
        });
        // Vista personalizada de sin permiso para usuarios (usando el helper de Laravel Auth)
        Route::get('/empleados', [UserController::class, 'index'])
            ->name('usuarios.index')
            ->middleware(['auth', 'can:ver empleados']);
        Route::get('/validar-usuario/{nombre_usuario}/{excluir?}', [UserController::class, 'validarUsuario']);
        Route::get('/validar-correo/{email}/{excluir?}', [UserController::class, 'validarCorreo']);
        Route::get('/mis-movimientos', function () {
            return redirect()->route('usuarios.movimientos', auth()->user()->slug);
        })->name('mis.movimientos');

        // Incidencias
        Route::controller(IncidenciaController::class)->group(function () {
            Route::get('/incidencias', 'index');
            Route::get('/incidencias/{slug}/edit/{persona_slug?}', 'edit')->name('incidencias.edit');
            Route::post('/filtrar-incidencia', 'filtrar')->name('filtrar.incidencia');
            Route::post('/incidencias/{slug}/atender', 'atender')->name('incidencias.atender')->middleware('can:cambiar estado de incidencias');
            Route::post('/incidencias/download', 'download')->name('incidencias.download');
            Route::get('/incidencias/descargar/{slug}', 'descargar')->name('incidencias.descargar')->middleware('can:descargar grafica incidencia');
            Route::post('/filtrar-incidencias-por-fechas', 'filtrarPorFechas')->name('filtrar.incidencias.fechas');
            Route::post('/incidencias/generar-pdf', 'generarPDF')->name('incidencias.generarPDF')->middleware('can:descargar listado incidencias');
            Route::get('/incidencias/chart', 'showChart')->name('estadisticas')->middleware('can:ver grafica incidencia');
            Route::get('/persona/{slug}/incidencias/create', 'crear')->name('incidencias.crear');
            Route::post('/incidencias/buscar', 'buscar')->name('incidencias.buscar');
            Route::get('/incidencias/{slug}/atender', 'atenderVista')->name('incidencias.atender.vista')->middleware(['incidencia.no-atendida', 'can:cambiar estado de incidencias']);
            Route::post('/incidencias/{slug}/atender', 'atenderGuardar')->name('incidencias.atender.guardar')->middleware('can:cambiar estado de incidencias');
            Route::get('/incidencias/{slug}/ver', 'ver')->name('incidencias.ver');
        });
        Route::resource('incidencias', IncidenciaController::class)->except(['show', 'create', 'edit', 'destroy'])->parameters(['incidencias' => 'slug']);
        Route::get('/instituciones-estaciones/direccion/{direccion}', [IncidenciaController::class, 'getInstitucionesEstacionesPorDireccion']);
        Route::get('/instituciones-estaciones/municipio/{municipio}', [IncidenciaController::class, 'getEstacionesPorMunicipio']);
        Route::get('/instituciones-estaciones/estado/{estado}/institucion/{institucion}', [IncidenciaController::class, 'getEstacionesPorEstadoEInstitucion']);
        Route::get('/incidencias-generales/create', [IncidenciaController::class, 'create'])->name('incidencias.create');
        Route::get('incidencias/{slug}/edit', [IncidenciaController::class, 'edit'])->name('incidencias.edit');
        Route::get('incidencia/{slug}', [IncidenciaController::class, 'show'])->name('incidencias.show');

        // Direcciones
        Route::controller(DomicilioController::class)->group(function () {
            Route::get('personas/agregardomicilio/{slug}', 'index')->name('personas.agregarDireccion');
            Route::post('personas/guardardomicilio/{id}', 'store')->name('guardarDireccion');
            Route::get('/personas/modificardomicilio/{slug}', 'edit')->name('personas.modificarDireccion');
            Route::post('/personas/actualizardomicilio/{id}/{idPersona}', 'update')->name('personas.actualizarDireccion');
            Route::post('/personas/marcarprincipal', 'marcarPrincipal')->name('personas.marcarPrincipal');
            Route::post('/check-lider-status', 'checkLiderStatus');
        });

        // Configuración
        Route::controller(configController::class)->group(function () {
            Route::get('/configuracion', 'index')->name('usuarios.configuracion');
            Route::post('/usuarios/{usuario}/cambiar', 'actualizar')->name('usuarios.cambiar');
            Route::post('/usuarios/{usuario}/cambiar-preguntas', 'cambiarPreguntas')->name('usuarios.cambiar-preguntas');
            Route::post('/usuarios/restaurar/{id_usuario}', 'restaurar')->name('usuarios.restaurar')->middleware('can:restaurar empleados');
        });

        // Notificaciones
        Route::prefix('notificaciones')->group(function () {
            Route::get('/', [NotificacionController::class, 'index'])->name('notificaciones.index');
            Route::get('/marcar-leida/{id}', [NotificacionController::class, 'marcarComoLeida'])->name('notificaciones.marcar-leida');
            Route::post('/marcar-todas-leidas', [NotificacionController::class, 'marcarTodasComoLeidas'])->name('notificaciones.marcar-todas-leidas');
            Route::get('/contador', [NotificacionController::class, 'getContadorNoLeidas'])->name('notificaciones.contador');
        });

        // Movimientos
        Route::controller(movimientoController::class)->group(function () {
            Route::get('/mis-movimientos', 'index')->name('mis.movimientos');
            Route::get('/mis-movimientos/exportar', 'exportar')->name('movimientos.exportar')->middleware('can:ver movimientos');
            Route::get('/mis-movimientos/descargar/{id}', 'descargar')->name('movimientos.descargar')->middleware('can:descargar detalles incidencias');
        });
        Route::get('/usuarios/{slug}/movimientos', [movimientoController::class, 'movimientosPorUsuario'])->name('movimientos.registradores')->middleware('can:ver movimientos empleados');

        // Otras rutas
        Route::resource('personal-reparacion', PersonalController::class, ['parameters' => ['slug']])
            ->middleware('can:ver personal');
        Route::get('/graficos/incidencias', [GraficoIncidenciasController::class, 'index'])->name('graficos.incidencias');
        Route::get('personal-reparacion/estaciones/{institucion}', [PersonalController::class, 'getEstacionesPorInstitucion'])
            ->name('personal-reparacion.estaciones');
        Route::post('/validar-cedula', [PersonaController::class, 'validarCedula'])->name('validar.cedula');
        Route::get('/personal-de-reparaciones/buscar/{cedula}', [PersonalController::class, 'buscar']);
        
        // Instituciones
        Route::controller(institucionController::class)->prefix('instituciones')->group(function () {
            Route::get('/', 'index')->name('instituciones.index')->middleware('can:ver instituciones');
            Route::put('/{id_institucion}/logo', 'updateLogo')->name('instituciones.updateLogo')->middleware('can:editar instituciones');
            Route::put('/{id_institucion}/membrete-pie', 'updateMembretePie')->name('instituciones.updateMembretePie')->middleware('can:editar instituciones');
        });
        
        Route::get('/incidencias/{id}/download', [IncidenciaController::class, 'downloadPdf'])
            ->name('incidencias.download');
        Route::get('/validar-cedula/{cedula}', [PersonalController::class, 'validarCedulaDirecta']);
        Route::post('/verificar-cedula', [PersonaController::class, 'verificarCedula'])->name('verificarCedula');
        Route::post('/verificar-correo', [PersonaController::class, 'verificarCorreo'])->name('verificarCorreo');
        Route::get('/api/estaciones-por-institucion/{id}', [InstitucionController::class, 'getByInstitucion']);
    });
    
    // Niveles de incidencia
    Route::resource('niveles-incidencia', nivelIncidenciaController::class)
        ->parameters(['niveles-incidencia' => 'nivelIncidencia'])
        ->middleware(['auth', 'can:ver niveles incidencias']);
        
    Route::put('niveles-incidencia/{nivelIncidencia}/toggle-status', [NivelIncidenciaController::class, 'toggleStatus'])
        ->name('niveles-incidencia.toggle-status')
        ->middleware(['auth', 'can:editar niveles incidencias']);
        
    Route::put('/{id_institucion}/pie',[NivelIncidenciaController::class, 'updatePie'])
        ->name('instituciones.updatePie')
        ->middleware(['auth', 'can:editar instituciones']);
        
    Route::get('/instituciones/estaciones/{institucionId}', [InstitucionController::class, 'getByInstitucion'])
        ->name('instituciones.estaciones');
});

Route::get('/graficos/incidencias/download', [IncidenciaController::class, 'downloadReport'])
    ->name('graficos.incidencias.download')
    ->middleware(['auth', 'can:descargar grafica incidencia']);

// Filtros AJAX para incidencias en dashboard
Route::get('/home/incidencias-temporales', [HomeController::class, 'incidenciasTemporales'])->name('home.incidencias.temporales');
Route::get('/home/incidencias-recientes', [HomeController::class, 'incidenciasRecientes'])->name('home.incidencias.recientes');

// API para tipos de incidencia (para el filtro)
Route::get('/api/tipos-incidencia', function() {
    return \App\Models\TipoIncidencia::orderBy('nombre')->get(['id_tipo_incidencia', 'nombre']);
});

// API para niveles de incidencia (para el filtro)
Route::get('/api/niveles-incidencia', function() {
    return \App\Models\NivelIncidencia::orderBy('nombre')->get(['id_nivel_incidencia', 'nombre']);
});

Route::get('/usuario/estado', function () {
    if (!auth()->check()) {
        return response()->json(['activo' => false]);
    }
    return response()->json([
        'activo' => auth()->user()->id_estado_usuario == 1
    ]);
})->middleware('auth');

Route::resource('empleados', EmpleadoAutorizadoController::class)->middleware('auth')->except(['index']);
Route::post('/empleados/verificar-cedula', [App\Http\Controllers\EmpleadoAutorizadoController::class, 'verificarCedula'])->name('empleados.verificarCedula');
Route::post('/usuarios/{id_usuario}/renovar-intentos', [App\Http\Controllers\UserController::class, 'renovarIntentos'])->name('usuarios.renovarIntentos');
// Permisos AJAX para frontend dinámico
Route::get('/usuario/permisos', [UserController::class, 'misPermisos'])->middleware('auth');

// Ruta para editar un empleado autorizado
Route::get('/empleados/{id}/edit', [EmpleadoAutorizadoController::class, 'edit'])->name('empleados.edit')->middleware(['auth', 'can:editar empleados']);