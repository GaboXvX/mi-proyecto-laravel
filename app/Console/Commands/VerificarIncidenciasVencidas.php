<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Incidencia;
use App\Models\EstadoIncidencia;
use App\Models\Notificacion;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class VerificarIncidenciasVencidas extends Command
{
    protected $signature = 'incidencias:verificar-vencidas';
    protected $description = 'Verifica fechas de vencimiento y actualiza estados de incidencias automáticamente';

    public function handle()
    {
        $hoy = Carbon::now();
        Log::info('Verificación iniciada. Fecha actual del servidor: ' . $hoy->toDateTimeString());

        // Obtener estados necesarios
        $estadoRetrasado = EstadoIncidencia::where('nombre', 'retrasado')->first();
        $estadoPendiente = EstadoIncidencia::where('nombre', 'pendiente')->first(); // Puedes cambiarlo a tu estado "normal"

        if (!$estadoRetrasado || !$estadoPendiente) {
            Log::error('Estado "retrasado" o "pendiente" no encontrado.');
            return;
        }

        $incidencias = Incidencia::whereNotNull('fecha_vencimiento')
            ->whereHas('estadoIncidencia', function ($q) {
                $q->where('nombre', '!=', 'atendido');
            })
            ->get();

        Log::info('Total incidencias a revisar: ' . $incidencias->count());

        foreach ($incidencias as $incidencia) {
            $vencida = $incidencia->fecha_vencimiento < $hoy;

            if ($vencida && $incidencia->id_estado_incidencia !== $estadoRetrasado->id_estado_incidencia) {
                // Pasar a estado "retrasado"
                $this->cambiarEstadoYNotificar($incidencia, $estadoRetrasado, 'La incidencia ha vencido su fecha límite y fue marcada como retrasada.');
            }

            if (!$vencida && $incidencia->id_estado_incidencia === $estadoRetrasado->id_estado_incidencia) {
                // Volver a estado "pendiente"
                $this->cambiarEstadoYNotificar($incidencia, $estadoPendiente, 'La incidencia ya no está vencida. Se ha restaurado su estado a pendiente.');
            }
        }

        $this->info('Verificación completada.');
    }

    protected function cambiarEstadoYNotificar(Incidencia $incidencia, EstadoIncidencia $nuevoEstado, string $mensaje)
{
    try {
        // Solo si el estado realmente cambia
        if ($incidencia->id_estado_incidencia !== $nuevoEstado->id_estado_incidencia) {

            $incidencia->id_estado_incidencia = $nuevoEstado->id_estado_incidencia;
            $incidencia->save();

            Log::info("Incidencia {$incidencia->id_incidencia} actualizada a estado '{$nuevoEstado->nombre}'.");

            // Solo notificar si el nuevo estado es "retrasado"
            if ($nuevoEstado->nombre === 'retrasado') {
                $notificacion = Notificacion::create([
                    'titulo' => 'Cambio de estado en incidencia',
                    'mensaje' => 'La incidencia #' . $incidencia->cod_incidencia . ': ' . $mensaje,
                    'tipo_notificacion' => 'estado_cambiado',
                    'id_incidencia' => $incidencia->id_incidencia,
                    'mostrar_a_todos' => true,
                ]);

                $notificacion->usuarios()->syncWithoutDetaching(User::pluck('id_usuario'));
                Log::info("Notificación creada para incidencia {$incidencia->id_incidencia}.");
            }
        }

    } catch (\Exception $e) {
        Log::error("Error actualizando incidencia {$incidencia->id_incidencia}: " . $e->getMessage());
    }
}


}
