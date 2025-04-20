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
        Schema::create('notificaciones_usuarios', function (Blueprint $table) {
            $table->unsignedBigInteger('id_notificacion');
            $table->unsignedBigInteger('id_usuario');
            $table->boolean('leido')->default(false);
            $table->timestamp('fecha_leido')->nullable();
            
            $table->foreign('id_notificacion')->references('id_notificacion')->on('notificaciones')->onDelete('cascade');
            $table->foreign('id_usuario')->references('id_usuario')->on('users')->onDelete('cascade');
            
            $table->primary(['id_notificacion', 'id_usuario']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notificacion_usuario');
    }
};
