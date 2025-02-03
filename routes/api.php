<?php

use App\Http\Controllers\Admin\Doctor\DoctorController;
use App\Http\Controllers\Admin\Patient\PatientController;
use App\Http\Controllers\Admin\Role\RoleController;
use App\Http\Controllers\Admin\Role\RoleListController;
use App\Http\Controllers\Admin\Service\ServiceController;
use App\Http\Controllers\Admin\Specialty\SpecialtyController;
use App\Http\Controllers\Admin\Staff\StaffController;
use App\Http\Controllers\Appointment\AppointmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Hour\HourController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group([
 
    // 'middleware' => 'api',
    'prefix' => 'auth',
   // 'middleware' => ['role:admin,api']
 
], function ($router) {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/refresh', [AuthController::class, 'refresh'])->name('refresh');
    Route::post('/me', [AuthController::class, 'me'])->name('me');
});


Route::group([
 
     'middleware' => 'auth:api',
    //'prefix' => 'auth',
   // 'middleware' => ['role:admin,api']
 
], function ($router) {
    Route::get('/roles/role-list-staff',[RoleListController::class,'selectStaff']);
    Route::get('/roles/role-list-doctor',[RoleListController::class,'selectDoctor']);
    Route::resource('/roles',RoleController::class);
    Route::post("staff/{id}",[StaffController::class,"update"]);
    Route::resource('/staff',StaffController ::class);
    Route::get('/specialty/specialty-list',[SpecialtyController::class,'index'])->name('specialty-list');
    Route::resource('/specialty',SpecialtyController ::class);
    Route::get('/doctor/schedule-hours',[DoctorController::class,'scheduleHour']);
    Route::post('/doctor/{id}',[DoctorController ::class,"update"]);
    Route::resource('/doctor',DoctorController ::class);
    Route::post('/patient/{id}',[PatientController ::class,"update"]);
    Route::resource('/patient',PatientController ::class);
    Route::resource('/service',ServiceController ::class);
    Route::get('/appointment/filter',[AppointmentController ::class, "filter"]);
    Route::resource('/appointment',AppointmentController ::class);
    Route::get('/appointment-hour',[HourController::class, "index"]);
});

