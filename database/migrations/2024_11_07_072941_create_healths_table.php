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
        Schema::create('healths', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class, 'doctor_id')->nullable();
            $table->foreignIdFor(User::class, 'patient_id')->nullable();
            $table->foreignIdFor(Clinic::class, 'clinic_id')->nullable();
            $table->date('checkup_date');
            $table->string('vision_right_eye')->nullable();
            $table->string('vision_left_eye')->nullable();
            $table->text('diagnosis')->nullable();
            $table->text('recommendations')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('healths');
    }
};
