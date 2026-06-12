<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incidents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('residence_id');
            $table->unsignedBigInteger('lot_id')->nullable();
            $table->string('titre');
            $table->longText('description');
            $table->enum('priorite', ['basse', 'moyenne', 'haute', 'critique']);
            $table->enum('statut', ['nouveau', 'en_cours', 'en_attente_prestataire', 'resolu', 'clos'])->default('nouveau');
            $table->unsignedBigInteger('assignee_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('residence_id')->references('id')->on('residences')->onDelete('cascade');
            $table->foreign('lot_id')->references('id')->on('lots')->onDelete('set null');
            $table->foreign('assignee_id')->references('id')->on('users')->onDelete('set null');
            $table->index(['tenant_id', 'residence_id', 'lot_id', 'assignee_id', 'statut', 'priorite'], 'idx_incidents_main');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incidents');
    }
};
