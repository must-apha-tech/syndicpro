<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('residence_access_codes', function (Blueprint $blueprint) {
            $blueprint->bigIncrements('id');
            $blueprint->bigInteger('tenant_id')->unsigned(); // Multi-tenancy
            $blueprint->bigInteger('residence_id')->unsigned();
            $blueprint->bigInteger('lot_id')->unsigned()->nullable();
            $blueprint->string('code', 6)->unique(); // Generated code like "ABC123"
            $blueprint->bigInteger('created_by')->unsigned();
            $blueprint->timestamp('expires_at')->nullable();
            $blueprint->bigInteger('used_by')->unsigned()->nullable();
            $blueprint->timestamp('used_at')->nullable();
            $blueprint->timestamps();

            // Foreign Keys
            $blueprint->foreign('residence_id')
                ->references('id')
                ->on('residences')
                ->onDelete('cascade');

            $blueprint->foreign('lot_id')
                ->references('id')
                ->on('lots')
                ->onDelete('set null');

            $blueprint->foreign('created_by')
                ->references('id')
                ->on('users')
                ->onDelete('restrict');

            $blueprint->foreign('used_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            // Indexes
            $blueprint->index('residence_id');
            $blueprint->index('lot_id');
            $blueprint->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('residence_access_codes');
    }
};
