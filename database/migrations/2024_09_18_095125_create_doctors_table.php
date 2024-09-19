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
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('middle_name')->nullable();
            $table->string('title')->nullable();
            $table->string('specialization')->nullable();
            $table->string('subspecialty')->nullable();
            $table->enum('gender', ['Male', 'Female', 'Other'])->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('email')->unique();
            $table->string('phone_number')->nullable();
            $table->longText('profile_description')->nullable();
            $table->string('education')->nullable();
            $table->integer('years_of_experience')->nullable();
            $table->json('consultation_availability')->nullable();
            $table->string('avatar_url')->nullable();
            $table->string('name')->virtualAs('concat(first_name, \' \', last_name)');
            $table->timestamps();
            $table->softDeletes(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
