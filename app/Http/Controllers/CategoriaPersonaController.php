<?php

namespace App\Http\Controllers;

use App\Models\CategoriaPersona;
use App\Models\ConfigReglaCategoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategoriaPersonaController extends Controller
{
    public function index()
    {
        $categorias = CategoriaPersona::with('reglasConfiguradas')->get();

        return view('categoriasPersonas.listaCategorias', compact('categorias'));
    }
    public function personasPorCategoria($id)
{
    $categoria = CategoriaPersona::with('personas')->findOrFail($id); // Obtiene la categoría con sus personas

    return response()->json([
        'personas' => $categoria->personas,
    ]);
}
public function getPersonasCount($id)
{
    // Asegúrate de usar withCount para obtener el número de personas asociadas a la categoría
    $categoria = CategoriaPersona::withCount('personas')->find($id);
    
    // Verifica si la categoría fue encontrada y devuelve la cantidad
    if ($categoria) {
        return response()->json([
            'personas_count' => $categoria->personas_count
        ]);
    }
    
    return response()->json(['error' => 'Categoría no encontrada'], 404);
}

public function create()
{
    return view('categoriasPersonas.agregarCategorias');
}

public function store(Request $request)
{
    $request->validate([
        'nombre_categoria' => 'required|string|max:255|unique:categorias_personas,nombre_categoria',
        'descripcion' => 'nullable|string|max:1000',
        'mensaje_error' => 'nullable|string|max:255',
    ]);

    // Generar el slug único
    $slug = Str::slug(Str::lower($request->nombre_categoria), '-');
    $originalSlug = $slug;
    $counter = 1;

    while (CategoriaPersona::where('slug', $slug)->exists()) {
        $slug = $originalSlug . '-' . $counter;
        $counter++;
    }

    DB::transaction(function () use ($request, $slug) {
        $categoria = CategoriaPersona::create([
            'nombre_categoria' => $request->nombre_categoria,
            'slug' => $slug,
            'descripcion' => $request->descripcion,
        ]);

        // Solo crear configuración si al menos una regla viene activada
        if (
            $request->has('requiere_comunidad') ||
            $request->has('unico_en_comunidad') ||
            $request->has('unico_en_sistema')
        ) {
            ConfigReglaCategoria::create([
                'id_categoria_persona' => $categoria->id_categoria_persona,
                'requiere_comunidad' => $request->has('requiere_comunidad'),
                'unico_en_comunidad' => $request->has('unico_en_comunidad'),
                'unico_en_sistema' => $request->has('unico_en_sistema'),
                'mensaje_error' => $request->mensaje_error,
            ]);
        }
    });

    return redirect()->route('categorias-personas.index')->with('success', 'Categoría creada correctamente');
}


public function edit($slug)
{
    $categoria = CategoriaPersona::with('reglasConfiguradas')->where('slug', $slug)->firstOrFail();

    return view('categoriasPersonas.modificarCategorias', compact('categoria'));
}
public function update(Request $request, $slug)
{
    try {
        $categoria = CategoriaPersona::where('slug', $slug)->firstOrFail();

        $request->validate([
            'nombre_categoria' => 'required|string|max:255|unique:categorias_personas,nombre_categoria,'.$categoria->id_categoria_persona.',id_categoria_persona',
            'descripcion' => 'nullable|string|max:1000',
            'mensaje_error' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($categoria, $request) {
            $categoria->update([
                'nombre_categoria' => $request->nombre_categoria,
                'descripcion' => $request->descripcion,
            ]);

            $reglas = $categoria->reglasConfiguradas ?: new ConfigReglaCategoria([
                'id_categoria_persona' => $categoria->id_categoria_persona
            ]);

            $reglas->requiere_comunidad = $request->has('requiere_comunidad');
            $reglas->unico_en_comunidad = $request->has('unico_en_comunidad');
            $reglas->unico_en_sistema = false;
            $reglas->mensaje_error = $request->mensaje_error;
            $reglas->save();
        });

        return redirect()->route('categorias-personas.index')
            ->with('success', 'Categoría actualizada correctamente');

    } catch (\Exception $e) {
        return redirect()->back()
            ->withInput()
            ->with('error', 'Error al actualizar la categoría: '.$e->getMessage());
    }
}

}

