<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('residences', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->string('name');
            $table->string('address');
            $table->string('city');
            $table->string('zip_code')->nullable();
            $table->integer('nb_lots');
            $table->unsignedBigInteger('syndic_id');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('syndic_id')->references('id')->on('users')->onDelete('restrict');
            $table->index(['tenant_id', 'syndic_id', 'city']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('residences');
    }
};
