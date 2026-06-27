<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('damaged_buildings', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('city', 150)->nullable();
            $table->string('zone', 150)->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('damage_level', 50)->nullable();
            $table->string('status', 50)->nullable()->default('reportado');
            $table->string('general_source', 255)->nullable();
            $table->text('notes')->nullable();
            $table->uuid('secure_token')->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('damaged_buildings');
    }
};
