<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PatientRfidPoint extends Model
{
    use SoftDeletes;

    protected $table = 'patient_rfid_points';
    
    public $fillable = [
        'user_id',
        'invoice_id',
        'rfid_number',
        'points',
        'status'
    ];

    PUBLIC CONST STATUS_ACTIVE = 'active';
    PUBLIC CONST STATUS_USED = 'used';
    PUBLIC CONST STATUS_INVALID = 'invalid';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }
}
