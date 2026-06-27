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
        Schema::create('need_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('need_id')->constrained('needs')->onDelete('cascade');
            $table->string('responder_name');
            $table->string('contact_info');
            $table->string('location')->nullable();
            $table->json('items_offered');
            $table->json('photos')->nullable();
            $table->uuid('secure_token')->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('need_responses');
    }
};
