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
        Schema::create('affected_zone_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affected_zone_id')->constrained('affected_zones')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('author_name');
            $table->text('message');
            $table->boolean('is_attended_change')->nullable();
            $table->string('status_change', 30)->nullable();
            $table->json('image_urls')->nullable();
            $table->uuid('secure_token')->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('affected_zone_updates');
    }
};
