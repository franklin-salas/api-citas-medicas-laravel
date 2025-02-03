<?php

namespace App\Http\Controllers\Admin\Specialty;

use App\Http\Controllers\Controller;
use App\Http\Resources\Specialty\SpecialtyResource;
use App\Http\Resources\Specialty\SpecialtySelectResource;
use App\Models\Specialty;
use Illuminate\Http\Request;

class SepecialtyListController extends Controller
{
    public function index(Request $request)
    {
        $specialties = Specialty::where('status','ACTIVO')->get();

        return response()->json([
            "data" => SpecialtySelectResource::collection($specialties)
        ]);
        
    }

}
