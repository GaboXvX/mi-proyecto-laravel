<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Incidencia;
use App\Models\EstadoIncidencia;
use App\Models\movimiento;
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
        $estadoPendiente = EstadoIncidencia::where('nombre', 'pendiente')->first();
        $estadoAtendido = EstadoIncidencia::where('nombre', 'atendido')->first();

        if (!$estadoRetrasado || !$estadoPendiente || !$estadoAtendido) {
            Log::error('Estados requeridos no encontrados.');
            return;
        }

        // Obtener incidencias no atendidas con fecha de vencimiento
        $incidencias = Incidencia::whereNotNull('fecha_vencimiento')
            ->where('id_estado_incidencia', '!=', $estadoAtendido->id_estado_incidencia)
            ->get();

        Log::info('Total incidencias a revisar: ' . $incidencias->count());

        foreach ($incidencias as $incidencia) {
            $vencida = $incidencia->fecha_vencimiento < $hoy;

            if ($vencida && $incidencia->id_estado_incidencia !== $estadoRetrasado->id_estado_incidencia) {
                // Pasar a estado "retrasado"
                $this->cambiarEstadoYNotificar($incidencia, $estadoRetrasado, 
                    'La incidencia ha vencido su fecha límite y fue marcada como retrasada.');
            }
            elseif (!$vencida && $incidencia->id_estado_incidencia === $estadoRetrasado->id_estado_incidencia) {
                // Volver a estado "pendiente" si ya no está vencida
                $this->cambiarEstadoYNotificar($incidencia, $estadoPendiente, 
                    'La incidencia ya no está vencida. Se ha restaurado su estado a pendiente.');
            }
        }

        $this->info('Verificación completada.');
    }

    protected function cambiarEstadoYNotificar(Incidencia $incidencia, EstadoIncidencia $nuevoEstado, string $mensaje)
    {
        try {
            // Solo si el estado realmente cambia
            if ($incidencia->id_estado_incidencia !== $nuevoEstado->id_estado_incidencia) {
                $estadoAnterior = $incidencia->estadoIncidencia->nombre;
                $incidencia->id_estado_incidencia = $nuevoEstado->id_estado_incidencia;
                $incidencia->save();

                Log::info("Incidencia {$incidencia->id_incidencia} cambiada de '{$estadoAnterior}' a '{$nuevoEstado->nombre}'.");

                // Crear notificación para cambios importantes
                $this->crearNotificacion($incidencia, $mensaje, $nuevoEstado->nombre);
                
                // Registrar movimiento en todos los casos
                $this->registrarMovimiento($incidencia, $nuevoEstado);
            }

        } catch (\Exception $e) {
            Log::error("Error actualizando incidencia {$incidencia->id_incidencia}: " . $e->getMessage());
        }
    }

    protected function crearNotificacion(Incidencia $incidencia, string $mensaje, string $nuevoEstado)
    {
        // Notificar solo para cambios a retrasado o de retrasado a pendiente
        if (in_array($nuevoEstado, ['retrasado', 'pendiente'])) {
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

    protected function registrarMovimiento(Incidencia $incidencia, EstadoIncidencia $nuevoEstado)
    {
        $movimiento = new movimiento();
        $movimiento->id_incidencia = $incidencia->id_incidencia;
        $movimiento->descripcion = 'Cambio de estado: ' . $nuevoEstado->nombre;
        $movimiento->save();
        Log::info("Movimiento registrado para incidencia {$incidencia->id_incidencia}.");
    }
}