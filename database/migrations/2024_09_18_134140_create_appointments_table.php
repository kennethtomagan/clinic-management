<?php

use App\Models\Clinic;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Slot;
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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->string('appointment_id')->unique();
            $table->string('description');
            $table->string('status')->default('created');
            $table->date('date');
            $table->float('fee')->nullable();
            $table->foreignIdFor(Doctor::class, 'doctor_id');
            $table->foreignIdFor(Patient::class);
            $table->foreignIdFor(Slot::class);
            $table->foreignIdFor(Clinic::class);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
