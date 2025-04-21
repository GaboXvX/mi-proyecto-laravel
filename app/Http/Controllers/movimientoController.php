<?php

namespace App\Http\Controllers;

use App\Models\Movimiento;
use App\Models\User;
use Illuminate\Http\Request;
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
                $query->whereNotNull('id_direccion');
                break;
            case 'incidencia':
                $query->whereNotNull('id_incidencia');
                break;
            case 'sistema':
                $query->whereNull('id_usuario_afectado')
                      ->whereNull('id_persona')
                      ->whereNull('id_direccion')
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

        // Filtro por rango
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

        // Filtros personalizados de fecha
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
                    $query->whereNotNull('id_direccion');
                    break;
                case 'incidencia':
                    $query->whereNotNull('id_incidencia');
                    break;
                case 'sistema':
                    $query->whereNull('id_usuario_afectado')
                          ->whereNull('id_persona')
                          ->whereNull('id_direccion')
                          ->whereNull('id_incidencia');
                    break;
            }
        }

        $movimientos = $query->get();

        $pdf = FacadePdf::loadView('pdf.movimientos', compact('movimientos'));
        return $pdf->download('movimientos_filtrados.pdf');
    }

    public function descargar($id, Request $request)
    {
        $mov = Movimiento::findOrFail($id);

        // Generar un PDF del movimiento
        $pdf = FacadePdf::loadView('pdf.movimiento', compact('mov'));

        return $pdf->download("movimiento-{$mov->id}.pdf");
    }
    // En MovimientoController.php
// app/Http/Controllers/movimientoController.php
public function movimientosRegistradores(Request $request)
{
    $query = Movimiento::with([
        'usuario', 
        'usuarioAfectado', 
        'persona', 
        'direccion', 
        'incidencia'
    ]);

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

    return view('usuarios.movimientos_registradores', compact('movimientos'));
}
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
                    $query->whereNotNull('id_direccion');
                    break;
                case 'incidencia':
                    $query->whereNotNull('id_incidencia');
                    break;
                case 'sistema':
                    $query->whereNull('id_usuario_afectado')
                          ->whereNull('id_persona')
                          ->whereNull('id_direccion')
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
