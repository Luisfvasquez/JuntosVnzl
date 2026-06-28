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
        Schema::create('evacuees', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->nullable(false);
            $table->string('last_name')->nullable();
            $table->string('dni')->nullable();
            $table->integer('age')->nullable();
            $table->string('gender')->nullable();
            $table->string('photo_url')->nullable();
            $table->string('current_location')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('origin_address')->nullable();
            $table->text('special_conditions')->nullable();
            $table->text('minor_medical_conditions')->nullable();
            $table->text('immediate_needs')->nullable();
            $table->integer('dependents_count')->nullable();
            $table->text('looking_for')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evacuees');
    }
};
