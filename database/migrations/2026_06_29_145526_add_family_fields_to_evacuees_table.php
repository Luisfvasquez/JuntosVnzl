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
        Schema::table('evacuees', function (Blueprint $table) {
            $table->unsignedBigInteger('head_of_family_id')->nullable()->after('id');
            $table->string('family_relationship', 50)->nullable()->after('head_of_family_id');

            $table->foreign('head_of_family_id')->references('id')->on('evacuees')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('evacuees', function (Blueprint $table) {
            $table->dropForeign(['head_of_family_id']);
            $table->dropColumn(['head_of_family_id', 'family_relationship']);
        });
    }
};
