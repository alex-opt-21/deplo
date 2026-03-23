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
    Schema::create('perfiles_usuario', function (Blueprint $table) {
        $table->id();
        // Relacionamos el perfil con un usuario de la tabla 'users'
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

        // Campos que necesitas para tu tarea RF-16
        $table->string('nombre');
        $table->string('apellido');
        $table->string('profesion')->nullable();
        $table->string('universidad')->nullable();
        $table->string('ubicacion')->nullable();
        $table->date('fecha_nacimiento')->nullable(); // Campo que pediste
        $table->string('foto_perfil')->nullable();

        $table->timestamps(); // created_at y updated_at
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perfiles_usuario');
    }
};
