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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('razon_social');
            $table->string('contacto')->nullable();
            $table->string('rfc', 13)->unique();
            $table->string('tel')->nullable();
            $table->string('email')->nullable();

            // Campos de Semáforos SAT (Vigencias)
            $table->date('fiel_vigencia')->nullable();
            $table->date('csd_vigencia')->nullable();
            
            // Rutas de archivos privados
            $table->text('csf')->nullable(); // Guardaremos la ruta del archivo
            $table->text('opinion')->nullable();
            $table->text('fiel')->nullable();
            $table->text('csd')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
