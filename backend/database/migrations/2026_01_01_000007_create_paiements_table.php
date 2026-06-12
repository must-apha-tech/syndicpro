<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paiements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('appel_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->decimal('amount', 12, 2);
            $table->datetime('date_paiement');
            $table->enum('mode', ['virement', 'cheque', 'especes', 'en_ligne']);
            $table->string('reference');
            $table->string('recu_pdf_path')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->foreign('appel_id')->references('id')->on('appels_de_fonds')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->index(['tenant_id', 'appel_id', 'user_id', 'date_paiement']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paiements');
    }
};
