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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();

            // Llaves Foráneas (Relaciones)
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('area_id')->constrained('areas')->onDelete('cascade');
            $table->foreignId('status_id')->constrained('statuses')->onDelete('cascade');
            $table->foreignId('priority_id')->constrained('priorities')->onDelete('cascade');

            // Atributos de la Tarea
            $table->string('title');
            $table->text('description');
            
            // Fechas y Seguimiento
            $table->timestamp('requested_at')->useCurrent(); // Se llena sola al crear
            $table->date('due_date')->nullable();            // Fecha compromiso
            $table->timestamp('completed_at')->nullable();   // Se llena al terminar

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
