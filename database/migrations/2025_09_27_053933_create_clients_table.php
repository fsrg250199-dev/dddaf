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
            $table->string('name');                          // Nombre del cliente
            $table->string('phone')->nullable();             // Teléfono
            $table->string('email')->nullable()->unique();   // Email (único si aplica)
            $table->string('address')->nullable();           // Dirección
            $table->text('notes')->nullable();               // Notas adicionales
            $table->boolean('is_active')->default(true);     // Activo/inactivo
            $table->timestamps();                            // created_at y updated_at
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
