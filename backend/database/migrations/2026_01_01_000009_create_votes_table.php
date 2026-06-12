<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('votes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('point_ordre_id');
            $table->unsignedBigInteger('proprietaire_id');
            $table->enum('decision', ['pour', 'contre', 'abstention']);
            $table->unsignedBigInteger('procuration_id')->nullable();
            $table->timestamp('voted_at')->nullable();
            $table->timestamps();

            $table->foreign('point_ordre_id')->references('id')->on('points_ordre')->onDelete('cascade');
            $table->foreign('proprietaire_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['tenant_id', 'point_ordre_id', 'proprietaire_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('votes');
    }
};
