<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('volunteers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('full_name', 150);
            $table->string('dni', 20);
            $table->string('phone', 20);
            $table->string('email', 150)->nullable();
            
            $table->string('skill_category', 50);
            $table->string('availability_description', 150);
            $table->string('modality', 20)->default('presencial');
            
            $table->text('location_description');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            
            $table->text('description');
            $table->string('status', 20)->default('disponible');
            $table->uuid('secure_token')->unique();
            $table->timestamps();

            $table->index('skill_category');
            $table->index('modality');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('volunteers');
    }
};
