<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\incidencia;
use App\Models\EstadoIncidencia;
use App\Models\Notificacion;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;  // Aquí la importación

class VerificarIncidenciasVencidas extends Command
{
    protected $signature = 'incidencias:verificar-vencidas';
    protected $description = 'Cambia el estado de incidencias vencidas';

    public function handle()
    {
        $hoy = Carbon::now();
        
        Log::info('Fecha actual: ' . $hoy);  // Usamos Log aquí

        $incidencias = incidencia::whereNotNull('fecha_vencimiento')
            ->where('fecha_vencimiento', '<', $hoy)
            ->whereHas('estadoIncidencia', function ($query) {
                $query->where('nombre', '!=', 'atendido');
            })
            ->get();

        Log::info('Incidencias encontradas: ' . $incidencias->count());

        $estadoRetrasado = EstadoIncidencia::where('nombre', 'retrasado')->first();

        if (!$estadoRetrasado) {
            Log::error('Estado "retrasado" no encontrado.');
            return;
        }

        foreach ($incidencias as $incidencia) {
            // Evitar procesar si ya está en estado "retrasado"
            if ($incidencia->id_estado_incidencia == $estadoRetrasado->id_estado_incidencia) {
                continue;
            }
        
            Log::info('Actualizando incidencia ID: ' . $incidencia->id_incidencia . ' con estado: retrasado.');
        
            $incidencia->id_estado_incidencia = $estadoRetrasado->id_estado_incidencia;
            $incidencia->save();
        
            $notificacion = Notificacion::create([
                'titulo' => 'Incidencia retrasada',
                'mensaje' => 'La incidencia con código #' . $incidencia->cod_incidencia . ' ha vencido su fecha límite y ha sido marcada como retrasada.',
                'tipo_notificacion' => 'incidencia',
                'id_incidencia' => $incidencia->id_incidencia,
                'mostrar_a_todos' => true
            ]);
        
            $usuarios = User::pluck('id_usuario');
            $notificacion->usuarios()->attach($usuarios);
        }
        

        $this->info('Incidencias vencidas actualizadas.');
    }
}
