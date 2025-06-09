<?php

namespace App\Http\Controllers;
use Illuminate\Support\Str;
use App\Http\Requests\updateUserRequest;
use App\Models\movimiento;
use App\Models\pregunta;
use App\Models\RespuestaDeSeguridad;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Contracts\Permission;
use Spatie\Permission\Models\Permission as ModelsPermission;
use Spatie\Permission\Models\Role;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use App\Models\Institucion;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (auth()->check() && auth()->user()->id_estado_usuario == 2) {
                Auth::logout();
                session()->invalidate();
                session()->regenerateToken();
                return redirect()->route('login')->with('error', 'Su usuario ha sido desactivado.');
            }
            // Si la ruta requiere permiso y no lo tiene, mostrar vista personalizada
            $route = $request->route();
            $action = $route ? $route->getAction() : [];
            if (isset($action['permission']) && !auth()->user()->can($action['permission'])) {
                return response()->view('errors.sin-permiso', [], 403);
            }
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $filtro = $request->input('filtro', 'todos');
        $usuarios = User::with(['empleadoAutorizado', 'roles'])->get();
        $idsUsuarios = $usuarios->pluck('empleadoAutorizado.id_empleado_autorizado')->filter()->toArray();
        $empleadosSinUsuario = \App\Models\EmpleadoAutorizado::with('cargo')
            ->whereNotIn('id_empleado_autorizado', $idsUsuarios)
            ->get();

        if ($filtro === 'registrados') {
            // Solo usuarios con empleadoAutorizado
            $usuarios = $usuarios->filter(fn($u) => $u->empleadoAutorizado)->values();
            $empleadosSinUsuario = collect();
        } elseif ($filtro === 'no_registrados') {
            // Solo empleados sin usuario
            $usuarios = collect();
            // $empleadosSinUsuario ya está correcto
        }

        $permisos = \Spatie\Permission\Models\Permission::all();
        $roles = \Spatie\Permission\Models\Role::all();

        // Si la petición es AJAX, devolver solo la tabla como HTML parcial
        if ($request->ajax()) {
            return response()->view('usuarios.partials.tabla_usuarios', [
                'usuarios' => $usuarios,
                'empleadosSinUsuario' => $empleadosSinUsuario
            ]);
        }

        return view('usuarios.listaUsuarios', compact('usuarios', 'roles', 'permisos', 'empleadosSinUsuario', 'filtro'));
    }

    public function create()
    {
        $preguntas=pregunta::all();

        return view('usuarios.registrarUsuarios', compact('preguntas'));
    }

    public function edit($slug)
    {
        $usuario = User::where('slug', $slug)->firstOrFail();
        return view('usuarios.modificarUsuarios', compact('usuario'));
    }

    public function update(updateUserRequest $request, $id_usuario)
    {
        $usuario = User::where('id_usuario', $id_usuario)->first();
        if (!$usuario) {
            return redirect()->route('usuarios.configuracion')->with('error', 'Usuario no encontrado');
        }

        try {
            // Verificar si el nombre o apellido ha cambiado para actualizar el slug
           
            // Actualizar los campos del usuario
          
            $usuario->email = $request->input('email');
            $usuario->nombre_usuario = $request->input('nombre_usuario');
            $usuario->password = bcrypt($request->input('contraseña'));

            $usuario->save();
            $movimiento = new movimiento();
            $movimiento->id_usuario = auth()->user()->id_usuario;
            $movimiento->descripcion = 'se actualizo un usuario';
            $movimiento->save();
            return redirect()->route('usuarios.configuracion')->with('success', 'Datos actualizados correctamente');
        } catch (\Exception $e) {
            return redirect()->route('usuarios.configuracion')->with('error', 'Error al actualizar los datos: ' . $e->getMessage());
        }
    }

    public function desactivar($id)
    {
        // Prevenir que un usuario se deshabilite a sí mismo
        if (auth()->check() && auth()->user()->id_usuario == $id) {
            // Si es una petición AJAX, responder con JSON
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No puedes deshabilitar tu propio usuario.'
                ], 403);
            }
            // Si es una petición normal, redirigir con error
            return redirect()->route('usuarios.index')->with('sweet_error', 'No puedes deshabilitar tu propio usuario.');
        }
        try {
            $usuario = User::where('id_usuario', $id)->first();
            $usuario->id_estado_usuario = 2; // Estado desactivado
            $usuario->save();
            $movimiento = new movimiento();
            $movimiento->id_usuario = auth()->user()->id_usuario;
            $movimiento->id_usuario_afectado = $usuario->id_usuario;
            $movimiento->descripcion = 'se desactivo un usuario';
            $movimiento->save();
            // Si el usuario desactivado es el autenticado, cerrar su sesión
            if (auth()->check() && auth()->user()->id_usuario == $usuario->id_usuario) {
                Auth::logout();
                session()->invalidate();
                session()->regenerateToken();
                return redirect()->route('login')->with('error', 'Su usuario ha sido desactivado.');
            }
            return redirect()->route('usuarios.index')->with('success', 'Usuario desactivado correctamente');
        } catch (\Exception $e) {
            return redirect()->route('usuarios.index')->with('error', 'Error al desactivar el usuario: ' . $e->getMessage());
        }
    }

    public function activar($id)
    {
        try {
            $usuario = User::where('id_usuario', $id)->first();

            // Eliminar verificación de roles
            $usuario->id_estado_usuario = 1; // Estado activo
            $usuario->save();
            $movimiento = new movimiento();
            $movimiento->id_usuario = auth()->user()->id_usuario;
            $movimiento->id_usuario_afectado = $usuario->id_usuario;
            $movimiento->descripcion = 'se activo un usuario';
            $movimiento->save();
            return redirect()->route('usuarios.index')->with('success', 'Usuario activado correctamente');
        } catch (\Exception $e) {
            return redirect()->route('usuarios.index')->with('error', 'Error al activar el usuario: ' . $e->getMessage());
        }
    }

    public function asignarPermiso(Request $request, $id_usuario)
    {
        $usuario = User::findOrFail($id_usuario);
        $permiso = $request->input('permiso');
    
        // Validar que el permiso exista
        if (!$permiso || !ModelsPermission::where('name', $permiso)->exists()) {
            return redirect()->route('usuarios.index')->with('error', 'Permiso no válido.');
        }
    
        // Asignar el permiso directamente al usuario
        $usuario->givePermissionTo($permiso);
        
        return redirect()->route('usuarios.index')->with('success', 'Permiso asignado correctamente.');
    }
    public function togglePermiso(Request $request, $id_usuario)
    {
        // Validar que el usuario que hace la petición sea admin
        

        $usuario = User::findOrFail($id_usuario);
        $permisoNombre = $request->input('permiso');

        // Validar que el permiso exista
        $permiso = ModelsPermission::where('name', $permisoNombre)->first();
        
        if (!$permiso) {
            return redirect()->route('usuarios.index')
                ->with('error', 'El permiso especificado no existe');
        }

        // Toggle del permiso (asignar si no lo tiene, revocar si lo tiene)
        if ($usuario->hasPermissionTo($permiso)) {
            $usuario->revokePermissionTo($permiso);
            $mensaje = "Permiso {$permiso->name} revocado correctamente";
        } else {
            $usuario->givePermissionTo($permiso);
            $mensaje = "Permiso {$permiso->name} asignado correctamente";
        }

        return redirect()->route('usuarios.index')->with('success', $mensaje);
    }

    public function asignarPermisosVista($id_usuario)
    {
        $usuario = User::findOrFail($id_usuario);
        $permisos = ModelsPermission::all();

        return view('usuarios.asignarPermisos', compact('usuario', 'permisos'));
    }

    public function togglePermisoAjax(Request $request)
    {
        $usuario = User::findOrFail($request->input('id_usuario'));
        $permisoNombre = $request->input('permiso');

        $permiso = ModelsPermission::where('name', $permisoNombre)->first();

        if (!$permiso) {
            return response()->json(['message' => 'El permiso especificado no existe'], 400);
        }

        if ($usuario->hasPermissionTo($permiso)) {
            $usuario->revokePermissionTo($permiso);
            $mensaje = "Permiso {$permiso->name} revocado correctamente.";
        } else {
            $usuario->givePermissionTo($permiso);
            $mensaje = "Permiso {$permiso->name} asignado correctamente.";
        }

        return response()->json(['message' => $mensaje]);
    }

    public function movimientos($slug)
    {
        // Buscar el usuario por el slug
        $usuario = User::where('slug', $slug)->first();

        if (!$usuario) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }

        // Verificación de permisos
        if (!auth()->user()->can('ver_movimientos') && auth()->id() != $usuario->id_usuario && !auth()->user()->hasRole('admin')) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        // Obtener los movimientos relacionados con el usuario
        $movimientos = Movimiento::where('id_usuario', $usuario->id_usuario)
                        ->select([
                            'id_movimiento',
                            'id_usuario',
                            'id_usuario_afectado',
                            'id_persona',
                            'id_direccion',
                            'id_incidencia',
                            'descripcion',
                            'created_at'
                        ])
                        ->orderBy('created_at', 'desc')
                        ->paginate(10);

        // Si la solicitud es AJAX o JSON, devolver una respuesta JSON
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'html' => view('usuarios.partials.movimientos_rows', compact('movimientos'))->render(),
                'pagination' => $movimientos->links()->toHtml(),
                'current_count' => $movimientos->count(),
                'total_count' => $movimientos->total()
            ]);
        }

        // Devolver la vista con los movimientos
        return view('usuarios.movimientos', compact('usuario', 'movimientos'));
    }

    public function downloadUsuariosPdf()
{
    // Obtener usuarios registrados con empleadoAutorizado
    $usuariosRegistrados = User::with('empleadoAutorizado')->get();
    
    // Obtener IDs de empleados que ya tienen usuario
    $idsUsuarios = $usuariosRegistrados->pluck('empleadoAutorizado.id_empleado_autorizado')->filter()->toArray();
    
    // Obtener empleados autorizados sin usuario
    $empleadosSinUsuario = \App\Models\EmpleadoAutorizado::with('cargo')
        ->whereNotIn('id_empleado_autorizado', $idsUsuarios)
        ->get();

    // Obtener la institución propietaria
    $institucionPropietaria = Institucion::where('es_propietario', 1)->first();

    // Obtener el logo de la institución en base64 (usando el mismo método que en IncidenciaController)
    $logoBase64 = null;
    if ($institucionPropietaria && $institucionPropietaria->logo_path) {
        $logoPath = public_path('storage/' . $institucionPropietaria->logo_path);
        if (file_exists($logoPath)) {
            $logoData = base64_encode(file_get_contents($logoPath));
            $logoBase64 = 'data:image/png;base64,' . $logoData;
        }
    }

    // Generar el PDF
    $pdf = Pdf::loadView('usuarios.listaUsuarios_pdf', [
        'usuariosRegistrados' => $usuariosRegistrados,
        'empleadosSinUsuario' => $empleadosSinUsuario,
        'logoBase64' => $logoBase64,
        'membrete' => optional($institucionPropietaria)->encabezado_html,
        'pie_html' => optional($institucionPropietaria)->pie_html,
    ])->setPaper('a4', 'landscape');

    return $pdf->download('lista_completa_empleados_' . now()->format('Ymd_His') . '.pdf');
}
    
    public function renovarIntentos($id_usuario)
    {
        $usuario = User::findOrFail($id_usuario);
        try {
            $usuario->intentos_renovacion = 0;
            $usuario->save();
            return redirect()->route('usuarios.index')->with('success', 'Intentos de recuperación renovados correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('usuarios.index')->with('error', 'No se pudo renovar los intentos: ' . $e->getMessage());
        }
    }

    // Devuelve los permisos actuales del usuario autenticado (para AJAX)
    public function misPermisos(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['permisos' => []]);
        }
        // Si es admin, devolver todos los permisos
        if ($user->hasRole('admin')) {
            $todos = \Spatie\Permission\Models\Permission::pluck('name');
            return response()->json(['permisos' => $todos]);
        }
        $permisos = $user->permissions->pluck('name');
        return response()->json(['permisos' => $permisos]);
    }
}
