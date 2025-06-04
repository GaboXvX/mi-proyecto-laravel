<?php

namespace App\Http\Controllers;

use App\Models\Institucion;
use App\Models\InstitucionEstacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class institucionController extends Controller
{
   public function index(Request $request)
{
    $instituciones = Institucion::orderBy('es_propietario', 'DESC')->paginate(4);
    
    return view('instituciones.listaInstituciones', compact('instituciones'));
}

    // Actualiza el logo de una institución
    public function updateLogo(Request $request, $id_institucion)
    {
        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png|max:2048',
        ]);

        $institucion = Institucion::findOrFail($id_institucion);

        // Elimina el logo anterior si existe
        if ($institucion->logo_path) {
            Storage::delete('public/' . $institucion->logo_path);
        }

        // Guarda el nuevo logo
        $path = $request->file('logo')->store('public/logos');
        $institucion->update(['logo_path' => str_replace('public/', '', $path)]);

        return back()->with('success', '¡Logo actualizado correctamente!');
    }

    // Actualiza el membrete (HTML)
    public function updateMembrete(Request $request, $id_institucion)
    {
        $institucion = Institucion::findOrFail($id_institucion);
        $institucion->update(['encabezado_html' => $request->encabezado_html]);

        return back()->with('success', '¡Membrete actualizado correctamente!');
    }

    // Actualiza el pie de página (footer HTML)
    public function updatePie(Request $request, $id_institucion)
    {
        $institucion = Institucion::findOrFail($id_institucion);
        $institucion->update(['pie_html' => $request->pie_html]);

        return back()->with('success', '¡Pie de página actualizado correctamente!');
    }

    // Unificado: Actualiza membrete y/o pie de página (solo si tienen valor)
    public function updateMembretePie(Request $request, $id_institucion)
    {
        $institucion = Institucion::findOrFail($id_institucion);
        $data = [];
        // Usar has() en vez de filled() para permitir borrar el campo si el usuario lo deja vacío
        if ($request->has('encabezado_html')) {
            $data['encabezado_html'] = $request->encabezado_html;
        }
        if ($request->has('pie_html')) {
            $data['pie_html'] = $request->pie_html;
        }
        if (!empty($data)) {
            $institucion->update($data);
            return back()->with('success', '¡Datos actualizados correctamente!');
        }
        return back();
    }

    public function getByInstitucion($institucionId)
    {
        $estaciones = InstitucionEstacion::where('id_institucion', $institucionId)
            ->select('id_institucion_estacion', 'nombre')
            ->get();

        return response()->json($estaciones);
    }
}
