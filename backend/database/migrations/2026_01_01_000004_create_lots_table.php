<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lots', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('residence_id');
            $table->string('numero');
            $table->enum('type', ['appartement', 'local', 'parking']);
            $table->decimal('surface', 8, 2);
            $table->integer('quote_part'); // in millièmes
            $table->unsignedBigInteger('proprietaire_id')->nullable();
            $table->integer('floor')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('residence_id')->references('id')->on('residences')->onDelete('cascade');
            $table->foreign('proprietaire_id')->references('id')->on('users')->onDelete('set null');
            $table->unique(['residence_id', 'numero']);
            $table->index(['tenant_id', 'residence_id', 'proprietaire_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lots');
    }
};
