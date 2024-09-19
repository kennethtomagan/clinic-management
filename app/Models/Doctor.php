<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Doctor extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'middle_name',
        'title',
        'specialization',
        'subspecialty',
        'gender',
        'date_of_birth',
        'email',
        'phone_number',
        'profile_description',
        'education',
        'years_of_experience',
        'available_in_person',
        'available_online',
        'avatar_url',
        'clinic_id'
    ];
    
    public function clinics(): BelongsToMany
    {
        return $this->belongsToMany(Clinic::class);
    }


    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class, 'doctor_id');
    }

}
