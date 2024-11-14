<?php

namespace Database\Seeders;

use App\Models\Clinic;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'type' => 'admin',
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@admin.com',
            'email_verified_at' => now(),
            'password' => Hash::make('Admin123'),
            'remember_token' => Str::random(10),
        ]);

        // Create clinic
        $clinic = Clinic::create([
            'name' => 'Eye Care Clinic',
            'address' => 'Rodriguez, Rizal',
            'zip' => '1234',
            'phone' => '09123654789',
        ]);

        $doctor = User::create([
            'type' => User::DOCTOR_TYPE,
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'email' => 'juan.delacruz@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('juan.delacruz@gmail.com'),
            'remember_token' => Str::random(10),
        ]);

        $doctor->doctorDetail()->create([
            'clinic_id' => $clinic->id,
            'education' => 'Bachelor of Science in Ophthalmology',
            'specialization' => 'Ophthalmology',
            'subspecialty' => 'Eye Diseases, Vision Correction, Microsurgery of the Eye',
            'years_of_experience' => 10,
            'status' => 'active',
            'profile_description' => 'Eye disease specialist'
        ]);

        $doctor2 = User::create([
            'type' => User::DOCTOR_TYPE,
            'first_name' => 'James',
            'last_name' => 'Reid',
            'email' => 'james.reid@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('james.reid@gmail.com'),
            'remember_token' => Str::random(10),
        ]);

        $doctor2->doctorDetail()->create([
            'clinic_id' => $clinic->id,
            'education' => 'Bachelor of Science in Ophthalmology',
            'specialization' => 'Ophthalmology',
            'subspecialty' => 'Eye Diseases, Vision Correction, Microsurgery of the Eye',
            'years_of_experience' => 10,
            'status' => 'active',
            'profile_description' => 'Eye disease specialist'
        ]);

    }
}
