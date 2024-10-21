<?php

namespace App\Http\Controllers\Api;

use App\Filament\Resources\DoctorResource;
use App\Http\Controllers\Controller;
use App\Http\Resources\ApiClinicResource;
use App\Models\Clinic;
use App\Models\Doctor;
use Illuminate\Http\Request;

class ClinicController extends Controller
{
    
    /**
     * Return List of clinics
     * 
     * @param Request $request
     * @return DoctorResource 
     */
    public function index(Request $request)
    {
        $clinics = Clinic::all();
        return ApiClinicResource::collection($clinics);
    }
}
