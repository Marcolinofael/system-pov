<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('atendimento_fotos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('atendimento_id')->constrained()->cascadeOnDelete();
            // Caminho no disco privado (storage/app/private)
            $table->string('caminho');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('atendimento_fotos');
    }
};
