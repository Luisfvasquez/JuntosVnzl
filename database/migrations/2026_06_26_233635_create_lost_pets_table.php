<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lost_pets', function (Blueprint $table) {
            $table->id();
            $table->string('reporter_name', 150);
            $table->string('contact_phone', 20);
            $table->string('pet_name', 100)->nullable();
            $table->string('species', 50)->default('perro');
            $table->string('breed', 100)->nullable();
            $table->text('description')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->text('address_description')->nullable();
            $table->string('status', 30)->default('perdido');
            $table->string('photo_url')->nullable();
            $table->uuid('secure_token')->unique();
            $table->timestamps();

            $table->index(['latitude', 'longitude']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lost_pets');
    }
};
