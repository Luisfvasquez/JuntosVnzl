<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('center_supplies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collection_center_id')->constrained('collection_centers')->cascadeOnDelete();
            $table->string('supply_name', 100);
            $table->decimal('quantity', 12, 2)->default(0.00);
            $table->string('unit_of_measurement', 20);
            $table->string('priority_level', 20)->default('estable');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('center_supplies');
    }
};
