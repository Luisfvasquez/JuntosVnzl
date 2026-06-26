<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('country_aids', function (Blueprint $table) {
            $table->id();
            $table->string('country_name', 100);
            $table->string('iso_code', 3)->unique();
            $table->string('aid_type', 100);
            $table->text('description')->nullable();
            $table->string('status', 20)->default('en_camino');
            $table->timestamp('registered_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('country_aids');
    }
};
