<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Product;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    
    public function getCounts()
    {
        
        return response()->json([
            'doctors' => Doctor::count(),
            'patients' => Patient::count(),
            'products' => Product::count(),
            'appointments' => Appointment::count(),
        ], 200);
    }
}
