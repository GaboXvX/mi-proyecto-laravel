<?php

namespace App\Http\Controllers;

use App\Models\Movimiento;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Institucion;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Barryvdh\DomPDF\PDF;

class movimientoController extends Controller
{
    public function index(Request $request)
{
    $query = Movimiento::where('id_usuario', auth()->id());

    // Filtro por rango
    if ($request->rango === 'ultimos_25') {
        $query->latest()->limit(25);
    } elseif ($request->rango == 'mes_actual') {
        $query->whereMonth('created_at', now()->month)
              ->whereYear('created_at', now()->year);
    } elseif ($request->rango == 'mes_pasado') {
        $query->whereMonth('created_at', now()->subMonth()->month)
              ->whereYear('created_at', now()->subMonth()->year);
    }

    // Filtros por fecha personalizada
    if ($request->filled('fecha_inicio')) {
        $query->whereDate('created_at', '>=', $request->fecha_inicio);
    }

    if ($request->filled('fecha_fin')) {
        $query->whereDate('created_at', '<=', $request->fecha_fin);
    }

    // Filtro por tipo de movimiento
    if ($request->filled('tipo')) {
        switch ($request->tipo) {
            case 'usuario':
                $query->whereNotNull('id_usuario_afectado');
                break;
            case 'persona':
                $query->whereNotNull('id_persona');
                break;
            case 'direccion':
                $query->whereNotNull('id_direccion_incidencia');
                break;
            case 'incidencia':
                $query->whereNotNull('id_incidencia');
                break;
            case 'sistema':
                $query->whereNull('id_usuario_afectado')
                      ->whereNull('id_persona')
                      ->whereNull('id_direccion_incidencia')
                      ->whereNull('id_incidencia');
                break;
        }
    }

    $movimientos = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

    return view('usuarios.movimientos', compact('movimientos'));
}

    public function exportar(Request $request)
    {
        $query = Movimiento::query();

        // Filtros
        if ($request->filled('usuario_slug')) {
            $usuario = User::where('slug', $request->usuario_slug)->firstOrFail();
            $query->where('id_usuario', $usuario->id_usuario);
        }

        switch ($request->rango) {
            case 'ultimos_25':
                $query->latest()->limit(25);
                break;
            case 'mes_actual':
                $query->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
                break;
            case 'mes_pasado':
                $query->whereMonth('created_at', now()->subMonth()->month)
                    ->whereYear('created_at', now()->subMonth()->year);
                break;
        }

        if ($request->filled('fecha_inicio')) {
            $query->whereDate('created_at', '>=', $request->fecha_inicio);
        }

        if ($request->filled('fecha_fin')) {
            $query->whereDate('created_at', '<=', $request->fecha_fin);
        }

        if ($request->filled('tipo')) {
            switch ($request->tipo) {
                case 'usuario':
                    $query->whereNotNull('id_usuario_afectado');
                    break;
                case 'persona':
                    $query->whereNotNull('id_persona');
                    break;
                case 'direccion':
                    $query->whereNotNull('id_direccion_incidencia');
                    break;
                case 'incidencia':
                    $query->whereNotNull('id_incidencia');
                    break;
                case 'sistema':
                    $query->whereNull('id_usuario_afectado')
                        ->whereNull('id_persona')
                        ->whereNull('id_direccion_incidencia')
                        ->whereNull('id_incidencia');
                    break;
            }
        }

        $movimientos = $query->get();

        // Membrete y pie
        $institucionPropietaria = Institucion::where('es_propietario', 1)->first();

        $logoBase64 = null;
        if ($institucionPropietaria && $institucionPropietaria->logo_path) {
            $logoPath = public_path('storage/' . $institucionPropietaria->logo_path);
            if (file_exists($logoPath)) {
                $logoData = base64_encode(file_get_contents($logoPath));
                $logoBase64 = 'data:image/png;base64,' . $logoData;
            }
        }

        $membrete = $institucionPropietaria->encabezado_html ?? '';
        $pie_html = $institucionPropietaria->pie_html ?? 'Generado el ' . now()->format('d/m/Y H:i:s');

        $pdf = FacadePdf::loadView('pdf.movimientos', [
            'movimientos' => $movimientos,
            'logoBase64' => $logoBase64,
            'membrete' => $membrete,
            'pie_html' => $pie_html,
        ])
        ->setOption('isRemoteEnabled', true)
        ->setOption('isHtml5ParserEnabled', true)
        ->setOption('defaultFont', 'DejaVu Sans');

        return $pdf->download('movimientos_filtrados.pdf');
    }

    public function descargar($id, Request $request)
    {
        $mov = Movimiento::findOrFail($id);

        $institucionPropietaria = Institucion::where('es_propietario', 1)->first();

        $logoBase64 = null;
        if ($institucionPropietaria && $institucionPropietaria->logo_path) {
            $logoPath = public_path('storage/' . $institucionPropietaria->logo_path);
            if (file_exists($logoPath)) {
                $logoData = base64_encode(file_get_contents($logoPath));
                $logoBase64 = 'data:image/png;base64,' . $logoData;
            }
        }

        $membrete = $institucionPropietaria->encabezado_html ?? '';
        $pie_html = $institucionPropietaria->pie_html ?? 'Generado el ' . now()->format('d/m/Y H:i:s');

        $pdf = FacadePdf::loadView('pdf.movimiento', [
            'mov' => $mov,
            'logoBase64' => $logoBase64,
            'membrete' => $membrete,
            'pie_html' => $pie_html,
        ])
        ->setOption('isRemoteEnabled', true)
        ->setOption('isHtml5ParserEnabled', true)
        ->setOption('defaultFont', 'DejaVu Sans');

        return $pdf->download("movimiento-{$mov->id}.pdf");
    }
    
    // En MovimientoController.php
// app/Http/Controllers/movimientoController.php

protected function applyFilters($query, Request $request)
    {
        // Filtro por rango
        if ($request->filled('rango')) {
            switch ($request->rango) {
                case 'ultimos_25':
                    $query->latest()->limit(25);
                    break;
                case 'mes_actual':
                    $query->whereMonth('created_at', now()->month)
                          ->whereYear('created_at', now()->year);
                    break;
                case 'mes_pasado':
                    $query->whereMonth('created_at', now()->subMonth()->month)
                          ->whereYear('created_at', now()->subMonth()->year);
                    break;
            }
        }

        // Filtros por fecha
        if ($request->filled('fecha_inicio')) {
            $query->whereDate('created_at', '>=', $request->fecha_inicio);
        }

        if ($request->filled('fecha_fin')) {
            $query->whereDate('created_at', '<=', $request->fecha_fin);
        }

        // Filtro por tipo de movimiento
        if ($request->filled('tipo')) {
            switch ($request->tipo) {
                case 'usuario':
                    $query->whereNotNull('id_usuario_afectado');
                    break;
                case 'persona':
                    $query->whereNotNull('id_persona');
                    break;
                case 'direccion':
                    $query->whereNotNull('id_direccion_incidencia');
                    break;
                case 'incidencia':
                    $query->whereNotNull('id_incidencia');
                    break;
                case 'sistema':
                    $query->whereNull('id_usuario_afectado')
                          ->whereNull('id_persona')
                          ->whereNull('id_direccion_incidencia')
                          ->whereNull('id_incidencia');
                    break;
            }
        }
    }
    // app/Http/Controllers/movimientoController.php
public function movimientosPorUsuario($slug, Request $request)
{
    // Buscar al usuario por su slug
    $usuario = User::where('slug', $slug)->firstOrFail();

    // Consulta de movimientos solo para este usuario
    $query = Movimiento::with([
            'usuario', 
            'usuarioAfectado', 
            'persona', 
            'direccion', 
            'incidencia'
        ])
        ->where('id_usuario', $usuario->id_usuario);

    // Aplicar filtros
    $this->applyFilters($query, $request);

    $movimientos = $query->orderByDesc('created_at')->paginate(15);

    if ($request->ajax()) {
        return response()->json([
            'html' => view('usuarios.partials.movimientos_rows', compact('movimientos'))->render(),
            'pagination' => $movimientos->links()->toHtml(),
            'current_count' => $movimientos->count(),
            'total_count' => $movimientos->total()
        ]);
    }

    return view('usuarios.movimientos_registradores', [
        'movimientos' => $movimientos,
        'usuario' => $usuario
    ]);
}
}