<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Clinic extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'address',
        'zip',
        'phone',
    ];

    public function doctors(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'doctor_details')
                    ->where('type', 'doctor');
    }

    public function schedules(): BelongsToMany
    {
        return $this->belongsToMany(Schedule::class);
    }

    public function doctorDetails()
    {
        return $this->hasMany(DoctorDetail::class, 'clinic_id');
    }
}
