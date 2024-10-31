<?php

use App\Models\Invoice;
use App\Models\PatientRfidPoint;
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
        Schema::create('patient_rfid_points', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Invoice::class, 'invoice_id')->nullable();
            $table->foreignIdFor(User::class, 'user_id')->nullable();
            $table->bigInteger('rfid_number')->nullable();
            $table->integer('points')->nullable();
            $table->string('status')->default(PatientRfidPoint::STATUS_ACTIVE);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_rfid_points');
    }
};
