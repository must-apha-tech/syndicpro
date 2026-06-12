<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('points_ordre', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('assemblee_id');
            $table->string('titre');
            $table->text('description')->nullable();
            $table->text('resultat')->nullable();
            $table->timestamps();

            $table->foreign('assemblee_id')->references('id')->on('assemblees')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('points_ordre');
    }
};
