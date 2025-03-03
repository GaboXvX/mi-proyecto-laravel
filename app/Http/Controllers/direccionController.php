<?php

namespace App\Http\Controllers;

use App\Models\Direccion;
use App\Models\Persona;
use Illuminate\Http\Request;

class direccionController extends Controller
{
    public function index(Request $request, $slug)
    {
        $persona = Persona::where('slug', $slug)->first();
    
        if (!$persona) {
            return redirect()->back()->withErrors(['error' => 'Persona no encontrada']);
        }
    
        return view('personas.agregarDireccion', compact('persona'));
    }
    
    public function store(Request $request, $id)
    {
        $direccion = new Direccion();
        $request->validate([
            'parroquia' => 'required|string|max:255',
            'urbanizacion' => 'required|string|max:255',
            'sector' => 'required|string|max:255',
            'comunidad' => 'required|string|max:255',
            'calle' => 'nullable|string|max:255',
            'manzana' => 'nullable|string|max:255',
            'numero_de_casa' => 'required|string|max:255',
        ]);

        if (empty($request->input('calle')) && empty($request->input('manzana'))) {
            return redirect()->back()->withErrors(['error' => 'Debe llenar al menos uno de los campos: calle o manzana']);
        }

        $direccionExistente = Direccion::where('id_parroquia', $request->input('parroquia'))
            ->where('id_urbanizacion', $request->input('urbanizacion'))
            ->where('id_sector', $request->input('sector'))
            ->where('id_comunidad', $request->input('comunidad'))
            ->where(function ($query) use ($request) {
                $query->where('calle', $request->input('calle'))
                      ->orWhere('manzana', $request->input('manzana'));
            })
            ->where('numero_de_casa', $request->input('numero_de_casa'))
            ->first();

        if ($direccionExistente) {
            return redirect()->back()->withErrors(['error' => 'Ya existe una dirección similar registrada']);
        }

        $direccion->id_parroquia = $request->input('parroquia');
        $direccion->id_urbanizacion = $request->input('urbanizacion');
        $direccion->id_sector = $request->input('sector');
        $direccion->id_comunidad = $request->input('comunidad');
        $direccion->calle = $request->input('calle');
        $direccion->manzana = $request->input('manzana');
        $direccion->numero_de_casa = $request->input('numero_de_casa');
        $direccion->id_persona = $id;

        $direccion->save();

        $persona = Persona::find($id);

        return redirect()->route('personas.show', ['slug' => $persona->slug])->with('success', 'Dirección registrada exitosamente');
    }
    public function edit(Request $request, $slug){
        $persona=Persona::where('slug',$slug)->first();
        if(!$persona){
            return redirect()->back()->withErrors(['error' => 'Persona no encontrada']);

        }
        return view('personas.modificarDirecciones',compact('persona'));
    }
    public function update(Request $request, $id){
$direccion=Direccion::where('id_direccion',$id)->first();
$request->validate([
    'parroquia' => 'required|string|max:255',
    'urbanizacion' => 'required|string|max:255',
    'sector' => 'required|string|max:255',
    'comunidad' => 'required|string|max:255',
    'calle' => 'nullable|string|max:255',
    'manzana' => 'nullable|string|max:255',
    'numero_de_casa' => 'required|string|max:255',
]);

if (empty($request->input('calle')) && empty($request->input('manzana'))) {
    return redirect()->back()->withErrors(['error' => 'Debe llenar al menos uno de los campos: calle o manzana']);
}

$direccion->id_parroquia = $request->input('parroquia');
$direccion->id_urbanizacion = $request->input('urbanizacion');
$direccion->id_sector = $request->input('sector');
$direccion->id_comunidad = $request->input('comunidad');
$direccion->calle = $request->input('calle');
$direccion->manzana = $request->input('manzana');
$direccion->numero_de_casa = $request->input('numero_de_casa');

$direccion->save();

$persona = Persona::find($direccion->id_persona);

return redirect()->route('personas.show', ['slug' => $persona->slug])->with('success', 'Dirección actualizada exitosamente');
    }}