<?php

namespace App\Models;

use App\Enums\AppointmentStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    use HasFactory;

    public $fillable = [
        'appointment_id',
        'patient_id',
        'slot_id',
        'clinic_id',
        'doctor_id',
        'date',
        'description',
        'status',
        'fee',
    ];

    protected $casts = [
        'status' => AppointmentStatus::class,
        'date' => 'datetime'
    ];

    public function slot(): BelongsTo
    {
        return $this->belongsTo(Slot::class);
    }

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id')->where('type', 'doctor');
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function scopeNew(Builder $query): void
    {
        $query->whereStatus(AppointmentStatus::Pending);
    }

}
