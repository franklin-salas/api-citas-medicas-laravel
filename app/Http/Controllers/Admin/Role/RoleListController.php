<?php

namespace App\Http\Controllers\Admin\Role;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleListController extends Controller
{
    public function selectDoctor() {
        $roles = Role::where("name","like","%DOCTOR%")->get();

        return response()->json([
            "data" => $roles
        ]);
    }

    public function selectStaff() {
        $roles = Role::where("name","not like","%DOCTOR%")->get();

        return response()->json([
            "data" => $roles
        ]);
    }

    
}
