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

class UserController extends Controller
{
    public function index(Request $request)
    {
        // Verificar si la solicitud es AJAX
        if ($request->ajax()) {
            $peticiones = User::where('id_estado_usuario', 3)
                              ->orWhere('id_estado_usuario', 4)
                              ->orWhere('id_estado_usuario', 1)
                              ->with(['estadoUsuario', 'empleadoAutorizado', 'roles']) // Cargar roles
                              ->get();

            return response()->json($peticiones);
        }

        // Si no es AJAX, cargar la vista como de costumbre
        $usuarios = User::with(['empleadoAutorizado', 'roles'])->get(); // Cargar roles
        $permisos = ModelsPermission::all(); // Obtener todos los permisos disponibles
        $roles = Role::all(); // Obtener todos los roles
        return view('usuarios.listaUsuarios', compact('usuarios', 'roles', 'permisos'));
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
        try {
            $usuario = User::where('id_usuario', $id)->first();
            
            // Eliminar verificación de roles
            $usuario->id_estado_usuario = 2; // Estado desactivado
            $usuario->save();
            $movimiento = new movimiento();
            $movimiento->id_usuario = auth()->user()->id_usuario;
            $movimiento->id_usuario_afectado = $usuario->id_usuario;
            $movimiento->descripcion = 'se desactivo un usuario';
            $movimiento->save();
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
}
