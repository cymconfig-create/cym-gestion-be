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
        Schema::create('selectors', function (Blueprint $table) {
            $table->increments('selector_id');
            $table->string('code', 16)->nullable()->unique();
            $table->string('name', 128);
            $table->integer('order')->nullable();
            $table->string('dad_selector_code', 16)->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('selectors');
    }
};
