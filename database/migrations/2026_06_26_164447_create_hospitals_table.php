<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hospitals', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('type', 50)->default('hospital_publico');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->text('address_description');
            $table->string('status', 30)->default('operativo');
            $table->string('contact_phone', 20)->nullable();
            $table->string('image_url')->nullable();
            $table->timestamps();

            $table->unique(['latitude', 'longitude'], 'hospitals_coords_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hospitals');
    }
};
