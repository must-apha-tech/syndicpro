<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('depenses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('exercice_id');
            $table->string('titre');
            $table->decimal('montant', 12, 2);
            $table->date('date_depense');
            $table->string('categorie');
            $table->timestamps();

            $table->foreign('exercice_id')->references('id')->on('exercices_comptables')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('depenses');
    }
};
