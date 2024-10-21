<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Panel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'type',
        'first_name',
        'last_name',
        'email',
        'email_verified_at',
        'password',
        'gender',
        'date_of_birth',
        'phone',
        'address',
        'avatar_url',
    ];

    PUBLIC CONST ADMIN_TYPE = 'admin';
    PUBLIC CONST PATIENT_TYPE = 'patient';
    PUBLIC CONST DOCTOR_TYPE = 'doctor';
    PUBLIC CONST RECEPTIONIST_TYPE = 'receptionist';


    PUBLIC CONST STATUS_ACTIVE = 'active';
    PUBLIC CONST STATUS_LEAVE = 'leave';
    PUBLIC CONST STATUS_RESIGNED = 'resigned';

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($user->email_verified_at)) {
                $model->email_verified_at = now();
            }
        });
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    public function doctorDetails()
    {
        return $this->hasOne(DoctorDetail::class, 'user_id');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class, 'doctor_id');
    }

    public function doctorDetail(): HasOne
    {
        return $this->hasOne(DoctorDetail::class, 'user_id');
    }
    
    /**
     * Check if the user is an admin.
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->type === self::ADMIN_TYPE;
    }

    /**
     * Check if the user is either an admin or receptionist.
     *
     * @return bool
     */
    public function isAdminOrReceptionist(): bool
    {
        return in_array($this->type, [self::ADMIN_TYPE, self::RECEPTIONIST_TYPE]);
    }


}
