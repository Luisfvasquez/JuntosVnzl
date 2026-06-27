<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('damaged_building_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('damaged_building_id')->constrained('damaged_buildings')->onDelete('cascade');
            $table->string('evaluator_name');
            $table->string('evaluator_credential')->nullable();
            $table->string('structural_damage');
            $table->string('habitability_status');
            $table->string('collapse_risk');
            $table->text('recommended_actions')->nullable();
            $table->text('comments')->nullable();
            $table->boolean('is_verifiable')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->json('image_urls')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('damaged_building_evaluations');
    }
};
