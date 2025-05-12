<?php

namespace App\Http\Controllers;

use App\Models\Institucion;
use App\Models\InstitucionEstacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class institucionController extends Controller
{
    // Muestra todas las instituciones con sus logos y membrêtes
    public function index()
    {
        $instituciones = Institucion::all();
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
     public function getByInstitucion($institucionId)
    {
        $estaciones = InstitucionEstacion::where('id_institucion', $institucionId)
            ->select('id_institucion_estacion', 'nombre')
            ->get();

        return response()->json($estaciones);
    }
}
