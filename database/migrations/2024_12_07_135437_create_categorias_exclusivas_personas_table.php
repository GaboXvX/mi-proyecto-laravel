<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('categorias_exclusivas_personas', function (Blueprint $table) {
            $table->bigIncrements('id_categoria_exclusiva');
            
            // Relaciones
            $table->unsignedBigInteger('id_persona');
            $table->unsignedBigInteger('id_categoria_persona');
            $table->unsignedBigInteger('id_comunidad')->nullable(); // Opcional según regla
            
            // Configuración de la regla
            $table->string('tipo_regla', 50); // Ej: 'unico_en_comunidad', 'unico_en_sistema', 'requiere_aprobar'
            $table->string('valor_regla')->nullable(); // Ej: ID de institución, permiso especial
            $table->boolean('es_activo')->default(true);
            
            // Auditoría
            $table->timestamp('fecha_aprobacion')->nullable();
            $table->unsignedBigInteger('id_usuario')->nullable();
            
            // Claves foráneas
            $table->foreign('id_persona')->references('id_persona')->on('personas')->onDelete('cascade');
            $table->foreign('id_categoria_persona')->references('id_categoria_persona')->on('categorias_personas');
            $table->foreign('id_comunidad')->references('id_comunidad')->on('comunidades');
            $table->foreign('id_usuario')->references('id_usuario')->on('users')->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reglas_especiales_personas');
    }
};
