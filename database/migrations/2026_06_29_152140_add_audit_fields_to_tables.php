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
        $tables = ['collection_centers', 'damaged_buildings', 'affected_zones', 'incidents'];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->string('audit_name', 150)->nullable();
                $table->string('audit_phone', 20)->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = ['collection_centers', 'damaged_buildings', 'affected_zones', 'incidents'];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropColumn(['audit_name', 'audit_phone']);
            });
        }
    }
};
