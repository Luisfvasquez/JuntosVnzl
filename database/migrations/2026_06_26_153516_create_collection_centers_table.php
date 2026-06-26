<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('collection_centers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('center_name', 150);
            $table->string('manager_name', 150);
            $table->string('dni', 20);
            $table->string('phone', 20);
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->text('address_description');
            $table->string('status', 30)->default('recibiendo_insumos');
            $table->uuid('secure_token')->unique();
            $table->timestamps();

            $table->index(['latitude', 'longitude']);
            $table->index(['dni', 'phone']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collection_centers');
    }
};
