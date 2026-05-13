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
        Schema::create('companies', function (Blueprint $table) {
            $table->increments('company_id');
            $table->string('code', 32);
            $table->string('nit', 16);
            $table->string('name', 144);
            $table->unsignedInteger('selector_person_type')->nullable();
            $table->unsignedInteger('selector_tax_regime')->nullable();
            $table->string('main_address', 128)->nullable();
            $table->string('complement_address', 64)->nullable();
            $table->unsignedInteger('department_address')->nullable();
            $table->unsignedInteger('city_address')->nullable();
            $table->string('phone_number', 16)->nullable();
            $table->string('cell_phone_number', 16)->nullable();
            $table->string('email_sgsst', 48)->nullable();
            $table->string('web_site', 128)->nullable();
            $table->string('code_ciiu', 16)->nullable();
            $table->integer('quantity_employees')->default(0);
            $table->unsignedInteger('selector_risk_level')->nullable();
            $table->unsignedInteger('selector_arl')->nullable();
            $table->unsignedInteger('legal_representative_id')->nullable();
            $table->unsignedInteger('system_manager_id')->nullable();
            $table->boolean('status')->default(true);
            $table->boolean('is_eliminated')->default(false);
            $table->string('created_by', 16)->nullable();
            $table->string('updated_by', 16)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
