<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assemblees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('residence_id');
            $table->enum('type', ['ordinaire', 'extraordinaire']);
            $table->datetime('date_heure');
            $table->string('lieu');
            $table->integer('quorum_requis')->default(25);
            $table->enum('statut', ['planifiee', 'en_cours', 'terminee'])->default('planifiee');
            $table->string('pv_pdf_path')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('residence_id')->references('id')->on('residences')->onDelete('cascade');
            $table->index(['tenant_id', 'residence_id', 'statut', 'date_heure']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assemblees');
    }
};
