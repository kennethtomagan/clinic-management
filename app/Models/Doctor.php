<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Doctor extends User
{

    protected $table = 'users';

    // You can define a global scope to filter by 'type'
    protected static function booted()
    {
        static::addGlobalScope('doctor', function ($query) {
            $query->where('type', User::DOCTOR_TYPE);
        });
    }
}
