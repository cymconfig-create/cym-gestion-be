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
        Schema::table('action_profiles', function (Blueprint $table) {
            $table->foreign('profile_id')->references('profile_id')->on('profiles');
            $table->foreign('action_id')->references('action_id')->on('actions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('action_profiles', function (Blueprint $table) {
            $table->dropForeign(['profile_id']);
            $table->dropForeign(['action_id']);
        });
    }
};