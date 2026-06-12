<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exercices_comptables', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('residence_id');
            $table->year('annee');
            $table->decimal('budget_total', 12, 2);
            $table->enum('statut', ['ouvert', 'clôturé'])->default('ouvert');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('residence_id')->references('id')->on('residences')->onDelete('cascade');
            $table->unique(['residence_id', 'annee']);
            $table->index(['tenant_id', 'residence_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exercices_comptables');
    }
};
