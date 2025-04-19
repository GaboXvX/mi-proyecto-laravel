<?php

use App\Http\Controllers\configController;
use App\Http\Controllers\direccionController;
use App\Http\Controllers\PersonaController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\IncidenciaController;
use App\Http\Controllers\LiderController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\movimientoController;
use App\Http\Controllers\NotificacionController;
use App\Http\Controllers\PeticionController;
use App\Http\Controllers\recuperarController;
use App\Http\Controllers\seguridadController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RecuperarGetController;
use App\Http\Controllers\RenovacionController;
use App\Http\Controllers\UsuarioValidacionController;
use App\Http\Controllers\UsuarioController;
use App\Models\Direccion;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'prevent-back-history'], function () {
    // routes/web.php

    Route::controller(RenovacionController::class)->group(function () {
        Route::get('/renovar-solicitud', 'mostrarFormulario')->name('renovacion.mostrar');
        Route::post('/renovar-solicitud', 'procesarFormulario')->name('renovacion.procesar');
    });
    Route::get('/buscar-empleado', [PeticionController::class, 'buscarEmpleado'])->name('buscar.empleado');
    Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/', [LoginController::class, 'authenticate'])->name('login.authenticate');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/login', [LoginController::class, 'index'])->middleware('guest');
    Route::get('registrar', [UserController::class, 'create'])->name('usuarios.create')->middleware('guest');
    Route::post('aceptar/{id}', [PeticionController::class, 'aceptar'])->name('peticion.aceptar');
    Route::resource('peticiones', PeticionController::class)->except('index');
    // Rutas de recuperación de contraseña
    Route::get('/recuperar-contraseña', [RecuperarController::class, 'ingresarCedula'])->name('recuperar.ingresarCedula')->middleware('guest');
    Route::post('/recuperar-contraseña/preguntas', [RecuperarController::class, 'procesarFormulario'])->name('recuperar.preguntas');
    Route::get('/recuperar-contraseña/redirigir', [RecuperarGetController::class, 'redirigirRecuperarClave'])->name('recuperar.redirigirRecuperarClave')->middleware('guest');
    // Ruta para validar la respuesta de seguridad (POST)
    Route::post('/recuperar/validar-respuesta', [RecuperarController::class, 'validarRespuesta'])->name('recuperar.validarRespuesta');
    Route::post('/cambiar-email/{usuarioId}', [RecuperarController::class, 'actualizarCorreo'])->name('cambiar.email');

    // Ruta para mostrar el cambio de contraseña (POST)
    Route::post('/recuperar-clave', [RecuperarController::class, 'mostrarCambioClave'])->name('recuperar.recuperarClave');

    Route::post('/cambiar-clave', [RecuperarController::class, 'mostrarCambioClave'])->name('cambiar-clave');
    Route::post('/cambiar-clave/{usuarioId}', [RecuperarController::class, 'update'])->name('cambiar.update');
    Route::get('/cambiar-clave/{token}', [RecuperarController::class, 'mostrarCambioClave'])->name('cambiar-clave');
    Route::post('/validar-campo-asincrono', [PeticionController::class, 'validarCampoAsincrono'])->name('validar.campo.asincrono');
    Route::get('/peticiones/obtener', [PeticionController::class, 'obtenerPeticiones'])->name('peticiones.obtener');
    Route::get('/home/total-peticiones', [HomeController::class, 'obtenerTotalPeticiones'])->name('home.totalPeticiones');
    Route::get('/validar-usuario/{nombre_usuario}/{excluir?}', [UsuarioController::class, 'validarUsuario']);
    Route::get('/validar-correo/{email}/{excluir?}', [UsuarioController::class, 'validarCorreo']);
    Route::middleware(['auth'])->group(function () {


        Route::get('/home', [HomeController::class, 'index'])->name('home');

        Route::get('peticiones', [PeticionController::class, 'index'])->name('peticiones.index');
        Route::post('/peticion/{id}', [PeticionController::class, 'rechazar'])->name('peticiones.rechazar');


        Route::resource('personas', PersonaController::class)->parameters(['personas' => 'slug'])->except('create');
        Route::post('/personas/validar-cedula', [PersonaController::class, 'validarCedula'])->name('personas.validar-cedula');
        Route::post('/personas/validar-correo', [PersonaController::class, 'validarCorreo'])->name('personas.validar-correo');
        Route::Post('/personas/buscar', [PersonaController::class, 'buscar'])->name('personas.buscar');
        Route::get('/persona/{slug}', [PersonaController::class, 'show'])->name('personas.show');
        Route::get('/personas/{slug}/incidencias', [PersonaController::class, 'verIncidencias'])->name('personas.incidencias');
        Route::get('/api/personas/{id}/direcciones', [PersonaController::class, 'obtenerDirecciones']);
        Route::resource('usuarios', UserController::class)
            ->except('create', 'store', 'update')
            ->middleware('can:ver empleados');
            Route::get('/usuarios/{slug}/movimientos', [UserController::class, 'movimientos'])->name('usuarios.movimientos');
        Route::post('/desactivar/{id}', [UserController::class, 'desactivar'])
            ->name('usuarios.desactivar')
            ->middleware('can:desactivar empleados');
            Route::get('/mis-movimientos', function () {
                return redirect()->route('usuarios.movimientos', auth()->user()->slug);
            })->name('mis.movimientos')->middleware('auth');
        Route::post('/activar/{id}', [UserController::class, 'activar'])
            ->name('usuarios.activar')
            ->middleware('can:habilitar empleados');
        Route::post('/usuarios/{id}/asignar-permiso', [UserController::class, 'asignarPermiso'])
            ->name('usuarios.asignarPermiso');
        Route::post('/usuarios/{usuario}/toggle-permiso', [UserController::class, 'togglePermiso'])
            ->name('usuarios.togglePermiso');
        Route::get('/usuarios/{id_usuario}/asignar-permisos', [UserController::class, 'asignarPermisosVista'])->name('usuarios.asignarPermisos');
        Route::post('/usuarios/toggle-permiso-ajax', [UserController::class, 'togglePermisoAjax'])->name('usuarios.togglePermisoAjax');
        Route::get('/incidencias', [IncidenciaController::class, 'index']);
       
        Route::resource('incidencias', IncidenciaController::class)
            ->except(['show', 'create', 'edit'])
            ->parameters(['incidencias' => 'slug']);
        Route::get('/incidencias/{slug}/edit/{persona_slug?}', [IncidenciaController::class, 'edit'])
            ->name('incidencias.edit')
            ->middleware('can:cambiar estado de incidencias');
        Route::post('/filtrar-incidencia', [IncidenciaController::class, 'filtrar'])
            ->name('filtrar.incidencia');
            Route::post('/incidencias/{slug}/atender', [IncidenciaController::class, 'atender'])
            ->name('incidencias.atender');
            
        Route::post('/incidencias/download', [IncidenciaController::class, 'download'])
            ->name('incidencias.download');
        Route::get('/incidencias/descargar/{slug}', [IncidenciaController::class, 'descargar'])
            ->name('incidencias.descargar')
            ->middleware('can:descargar grafica incidencia');
        Route::post('/filtrar-incidencias-por-fechas', [IncidenciaController::class, 'filtrarPorFechas'])
            ->name('filtrar.incidencias.fechas');
        Route::post('/incidencias/generar-pdf', [IncidenciaController::class, 'generarPDF'])
            ->name('incidencias.generarPDF')
            ->middleware('can:descargar listado incidencias');
        Route::get('/incidencias/chart', [IncidenciaController::class, 'showChart'])
            ->name('estadisticas')
            ->middleware('can:ver grafica incidencia');
        Route::get('/persona/{slug}/incidencias/create', [IncidenciaController::class, 'crear'])->name('incidencias.crear');
        Route::get('persona/{slug}/incidencia/{incidencia_slug}', [IncidenciaController::class, 'show'])->name('incidencias.show');
        Route::get('personas/agregardirecion/{slug}', [direccionController::class, 'index'])->name('personas.agregarDireccion');
        Route::post('personas/guardardireccion/{id}', [direccionController::class, 'store'])->name('guardarDireccion');
        Route::get('/personas/modificardireccion/{slug}', [direccionController::class, 'edit'])->name('personas.modificarDireccion');
        Route::post('/personas/actualizardireccion/{id}/{idPersona}', [direccionController::class, 'update'])->name('personas.actualizarDireccion');
        Route::post('/personas/marcarprincipal', [direccionController::class, 'marcarPrincipal'])->name('personas.marcarPrincipal');
        Route::get('/incidencias/{slug}', [IncidenciaController::class, 'mostrar'])->name('incidencias.mostrar');
        Route::post('/incidencias/buscar', [IncidenciaController::class, 'buscar'])->name('incidencias.buscar');
        Route::resource('lideres', liderController::class)->except('update', 'create');
        Route::put('/lideres/update/{slug}', [liderController::class, 'update'])->name('lideres.update');
        Route::post('/lideres/buscar', [liderController::class, 'buscar'])->name('lideres.buscar');
        Route::get('/registrarincidenciaslider/{slug}', [IncidenciaController::class, 'create'])->name('incidenciaslider.create');
        Route::get('/modificarincidencialider/{slug}', [IncidenciaController::class, 'edit'])->name('incidenciaslider.edit');

        Route::get('/configuracion', [configController::class, 'index'])->name('usuarios.configuracion');
        Route::post('/usuarios/{usuario}/cambiar', [ConfigController::class, 'actualizar'])->name('usuarios.cambiar');
        Route::post('/usuarios/{usuario}/cambiar-preguntas', [ConfigController::class, 'cambiarPreguntas'])->name('usuarios.cambiar-preguntas');
        Route::post('/usuarios/cambiar/{id_usuario}', [UserController::class, 'update'])->name('usuarios.cambiar');
        Route::post('/usuarios/restaurar/{id_usuario}', [configController::class, 'restaurar'])->name('usuarios.restaurar')->middleware('can:restaurar usuarios');
        Route::post('/check-lider-status', [direccionController::class, 'checkLiderStatus']);
        Route::post('/incidencias/generar-pdf', [IncidenciaController::class, 'generarPDF'])->name('incidencias.generarPDF');
        Route::prefix('notificaciones')->middleware('auth')->group(function() {
            Route::get('/', [NotificacionController::class, 'index'])->name('notificaciones.index');
            Route::get('/marcar-leida/{id}', [NotificacionController::class, 'marcarComoLeida'])->name('notificaciones.marcar-leida');
            Route::post('/marcar-todas-leidas', [NotificacionController::class, 'marcarTodasComoLeidas'])->name('notificaciones.marcar-todas-leidas');
            Route::get('/contador', [NotificacionController::class, 'getContadorNoLeidas'])->name('notificaciones.contador');
        });
        Route::get('/mis-movimientos', [MovimientoController::class, 'index'])->name('mis.movimientos');
Route::get('/mis-movimientos/exportar', [MovimientoController::class, 'exportar'])->name('movimientos.exportar');
Route::get('/mis-movimientos/descargar/{id}', [MovimientoController::class, 'descargar'])->name('movimientos.descargar');

    });
    // Para ocultar individual

});
