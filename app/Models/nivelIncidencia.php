<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class nivelIncidencia extends Model
{
    use HasFactory;
    protected $table = 'niveles_incidencias';
    protected $primaryKey = 'id_nivel_incidencia';
    protected $fillable = [
        'nivel',
        'nombre',
        'descripcion',
        'horas_vencimiento',
        'frecuencia_recordatorio',
        'color',
        'activo'
    ];
    /**
     * Relación uno a muchos con Incidencia.
     */
    public function incidencias()
    {
        return $this->hasMany(incidencia::class, 'id_nivel_incidencia', 'id_nivel_incidencia');
    }

    /**
     * Verifica si el nombre es igual o muy similar a otro nivel existente, considerando acentos y mayúsculas/minúsculas.
     * Devuelve true si hay coincidencia exacta o similaridad >= 90%.
     */
    public static function nombreEsSimilar($nombreNuevo, $excluirId = null)
    {
        // Normaliza y elimina espacios, mayúsculas, minúsculas y acentos
        $nombreNuevoNormalizado = preg_replace('/\s+/', '', self::normalizarNombre($nombreNuevo));
        $niveles = self::query();
        if ($excluirId) {
            $niveles->where('id_nivel_incidencia', '!=', $excluirId);
        }
        $nombresExistentes = $niveles->pluck('nombre');
        foreach ($nombresExistentes as $nombreExistente) {
            $nombreExistenteNormalizado = preg_replace('/\s+/', '', self::normalizarNombre($nombreExistente));
            // Coincidencia exacta tras normalizar y quitar espacios
            if ($nombreNuevoNormalizado === $nombreExistenteNormalizado) {
                return true;
            }
            // Similaridad >= 90% tras normalizar y quitar espacios
            similar_text($nombreNuevoNormalizado, $nombreExistenteNormalizado, $percent);
            if ($percent >= 90) {
                return true;
            }
        }
        return false;
    }

    /**
     * Normaliza un nombre quitando acentos y convirtiendo a minúsculas.
     */
    public static function normalizarNombre($nombre)
    {
        $nombre = mb_strtolower($nombre, 'UTF-8');
        $nombre = preg_replace('/[áàäâ]/u', 'a', $nombre);
        $nombre = preg_replace('/[éèëê]/u', 'e', $nombre);
        $nombre = preg_replace('/[íìïî]/u', 'i', $nombre);
        $nombre = preg_replace('/[óòöô]/u', 'o', $nombre);
        $nombre = preg_replace('/[úùüû]/u', 'u', $nombre);
        $nombre = preg_replace('/[ñ]/u', 'n', $nombre);
        $nombre = preg_replace('/[^a-z0-9 ]/', '', $nombre); // Elimina otros caracteres especiales
        return trim($nombre);
    }
}
