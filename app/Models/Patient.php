<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
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
        'gender',
        'date_of_birth',
        'email',
        'phone',
        'avatar_url',
        'address',
    ];
    
    // /**
    //  * Get the doctor full name
    //  */
    // protected function fullName(): Attribute
    // {
    //     return Attribute::make(
    //         get: fn () => $this->first_name .' ' . $this->last_name
    //     );
    // }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

}
