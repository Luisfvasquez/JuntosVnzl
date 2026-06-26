<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aid_offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('provider_name', 150);
            $table->string('dni', 20);
            $table->string('phone', 20);
            $table->string('title', 150);
            $table->text('description');
            $table->string('category', 50);
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->text('address_description');
            $table->uuid('secure_token')->unique();
            $table->string('status', 20)->default('disponible');
            $table->timestamps();

            $table->index(['latitude', 'longitude']);
            $table->index(['dni', 'phone']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aid_offers');
    }
};
