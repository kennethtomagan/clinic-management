<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppointmentLog extends Model
{

    protected $table = 'appointment_logs';
    
    public $fillable = [
        'appointment_id',
        'type',
        'log',
    ];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class, 'appointment_id');
    }
}
