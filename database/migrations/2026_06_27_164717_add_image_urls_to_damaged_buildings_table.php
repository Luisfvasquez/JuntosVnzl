<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('damaged_buildings', function (Blueprint $table) {
            $table->json('image_urls')->nullable()->after('notes');
            $table->boolean('is_verified')->default(false)->after('image_urls');
        });
    }

    public function down(): void
    {
        Schema::table('damaged_buildings', function (Blueprint $table) {
            $table->dropColumn(['image_urls', 'is_verified']);
        });
    }
};
