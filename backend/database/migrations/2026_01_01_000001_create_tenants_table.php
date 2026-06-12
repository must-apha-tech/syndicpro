<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $blueprint) {
            $blueprint->bigIncrements('id');
            $blueprint->string('name');
            $blueprint->string('database_name')->unique();
            $blueprint->boolean('is_active')->default(true);
            $blueprint->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
