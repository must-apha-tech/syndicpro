<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appels_de_fonds', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('exercice_id');
            $table->unsignedBigInteger('lot_id');
            $table->string('numero');
            $table->decimal('amount', 12, 2);
            $table->datetime('date_emission');
            $table->datetime('date_echeance');
            $table->enum('statut', ['emis', 'partiel', 'paye'])->default('emis');
            $table->decimal('reliquat', 12, 2)->default(0);
            $table->timestamps();

            $table->foreign('exercice_id')->references('id')->on('exercices_comptables')->onDelete('cascade');
            $table->foreign('lot_id')->references('id')->on('lots')->onDelete('cascade');
            $table->index(['tenant_id', 'exercice_id', 'lot_id', 'statut', 'date_echeance'], 'idx_appels_main');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appels_de_fonds');
    }
};
