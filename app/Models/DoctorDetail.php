<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DoctorDetail extends Model
{
    use HasFactory;

    protected $table = 'doctor_details';

    protected $casts = [
        'consultation_availability' => 'array',
    ];
    
    protected $fillable = [
        'clinic_id',
        'user_id',
        'education',
        'specialization',
        'subspecialty',
        'years_of_experience',
        'consultation_availability',
        'profile_description'
    ];

    // Relationship with User model
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relationship with Clinic model
    public function clinic()
    {
        return $this->belongsTo(Clinic::class, 'clinic_id');
    }

}
