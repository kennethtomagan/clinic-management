<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Health extends Model
{
    use HasFactory;
    
    public $fillable = [
        'doctor_id',
        'patient_id',
        'vision_right_eye',
        'vision_left_eye',
        'checkup_date',
        'diagnosis',
        'recommendations',
        'notes',
    ];

    protected $casts = [
        'date' => 'datetime'
    ];


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
}
