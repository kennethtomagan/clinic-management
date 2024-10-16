<?php

use App\Models\Clinic;
use App\Models\User;
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
        Schema::create('doctor_details', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Clinic::class, 'clinic_id')->before('avatar_url')->nullable();
            $table->foreignIdFor(User::class, 'user_id')->before('avatar_url')->nullable();
            $table->string('education')->nullable();
            $table->string('specialization')->nullable();
            $table->string('subspecialty')->nullable();
            $table->integer('years_of_experience')->nullable();
            $table->json('consultation_availability')->nullable();
            $table->longText('profile_description')->nullable();
            $table->timestamps();
            $table->softDeletes(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_details');
    }
};
