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
        Schema::table('messages', function (Blueprint $table) {
            $table->foreign('conversation_id')->references('conversation_id')->on('conversations');
            $table->foreign('user_id')->references('user_id')->on('users');
            $table->foreign('attachment_id')->references('attachment_id')->on('attachments');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropForeign(['conversation_id']);
            $table->dropForeign(['user_id']);
            $table->dropForeign(['attachment_id']);
        });
    }
};
