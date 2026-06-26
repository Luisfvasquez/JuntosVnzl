<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incidents', function (Blueprint $table) {
            $table->id();
            $table->string('reporter_name', 150)->default('Anónimo');
            $table->text('description');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->text('address_description');
            $table->string('severity', 20)->default('moderado');
            $table->string('aid_status', 40)->default('sin_ayuda');
            $table->text('aid_type_received')->nullable();
            $table->integer('verified_by_community_count')->default(1);
            $table->timestamps();

            $table->index(['latitude', 'longitude']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incidents');
    }
};
