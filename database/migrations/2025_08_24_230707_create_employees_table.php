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
        Schema::create('employees', function (Blueprint $table) {
            $table->increments('employee_id');
            $table->string('full_name', 64);
            $table->unsignedInteger('selector_identification');
            $table->string('identification_number', 16)->unique();
            $table->string('place_of_issue', 128)->nullable();
            $table->string('residence_address', 128)->nullable();
            $table->string('complement_address', 64)->nullable();
            $table->string('city_address', 64)->nullable();
            $table->string('department_address', 64)->nullable();
            $table->date('birthdate')->nullable();
            $table->string('place_of_birth', 128)->nullable();
            $table->string('email', 48)->nullable();
            $table->string('phone_number', 16)->nullable();
            $table->string('cell_phone_number', 16)->nullable();
            $table->unsignedInteger('selector_academic_level')->nullable();
            $table->unsignedInteger('selector_arl')->nullable();
            $table->unsignedInteger('selector_eps')->nullable();
            $table->unsignedInteger('selector_pension_fund')->nullable();
            $table->unsignedInteger('selector_severance_fund')->nullable();
            $table->unsignedInteger('selector_type_of_contract')->nullable();
            $table->string('job_position', 64)->nullable();
            $table->dateTime('contract_date')->nullable();
            $table->unsignedInteger('selector_blood_type')->nullable();
            $table->string('allergies', 128)->nullable();
            $table->unsignedInteger('selector_civil_status')->nullable();
            $table->unsignedInteger('selector_identification_contact')->nullable();
            $table->string('identification_number_contact', 16)->nullable();
            $table->string('full_name_contact', 64)->nullable();
            $table->string('phone_number_contact', 16)->nullable();
            $table->string('cell_phone_number_contact', 16)->nullable();
            $table->string('email_contact', 48)->nullable();
            $table->boolean('status')->default(true);
            $table->unsignedInteger('company_id')->nullable();
            $table->unsignedInteger('user_id')->nullable();
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
        Schema::dropIfExists('employees');
    }
};
