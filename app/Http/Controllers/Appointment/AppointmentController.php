<?php

namespace App\Http\Controllers\Appointment;

use App\Http\Controllers\Controller;
use App\Http\Resources\Appointment\DoctorAppointmentResource;
use App\Models\Appointment;
use App\Models\DoctorSchedule;
use App\Models\ScheduleDay;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function filter(Request $request)
    {
        $date_appointment =$request->date_appointment;
        $hour = $request->hour_id;
        $specialty_id= $request->specialty_id;
        Carbon::setLocale('es');
        DB::statement("SET lc_time_names = 'es_ES'");

        $name_day = Carbon::parse($date_appointment)->dayName;
        // $doctor_query = ScheduleDay:: where("day","like","%".$name_day."%")
        //                             ->WhereHas("doctor",function($q) use($specialty_id){
        //                                 $q->where("specialty_id",$specialty_id);
        //                             })
        //                             ->whereHas("scheduleHours", function($q) use($hour){
        //                                 $q->whereHas("doctorScheduleHour", function($qs) use($hour){
        //                                     $qs ->where("hour", $hour);
        //                                 });

        //                             })->get();

      
     $query  = User::where('specialty_id', $specialty_id)
                    ->join('specialties','users.specialty_id','=','specialties.id')
                    ->join('schedule_days','users.id' ,'=', 'schedule_days.user_id')
                    ->join('schedule_hours','schedule_days.id', '=','schedule_hours.schedule_day_id')
                    ->join('doctor_schedules','schedule_hours.doctor_schedule_id','=','doctor_schedules.id')
                
                    ->where('schedule_days.day','like',"%".$name_day."%")
                    ->where('doctor_schedules.hour',$hour)
                    ->select('users.id as id',"users.avatar",'specialties.name as specialty_name', DB::raw("CONCAT(users.name,' ',users.last_name) as full_name"),
                    DB::raw('COUNT(*)  AS  avalible'))
                    ->groupBy('users.id', 'users.avatar', 'specialties.name');

                    if ($request->has('sort_direction')) {
                        $sortDirection = $request->input('sort_direction');
                        if ($sortDirection == '') {
                            $request['sort_direction'] = 'desc';
                            $request['sort_by'] = 'id';
                        }
                        $sortBy = $request->input('sort_by');
                        $sortDirection = $request->input('sort_direction');
                        $query->orderBy( $sortBy, $sortDirection);
                    }else {
                        $query->orderBy('id', 'desc');
                    }
            
                 
            
                    $perPage = $request->input('per_page', 10);
                    $doctor_query = $query->paginate($perPage);
            //  dd($doctor_query);
        //     $doctor_list = $doctor_query->items();
        //   foreach ($doctor_list as $doctor) {
        //     $count_appointment = Appointment::where('doctor_id', $doctor->user_id)
        //     ->whereDate('date_appointment',$date_appointment)
        //     ->where('hour',$hour)
        //     ->whereIn('status',['ACTIVO','PAGADO'])->count();
        //     $doctor->avalible = $doctor->avalible  - 1;
        //     //  $doctor_list[$key]= $doctor->avalible  - 1;            
            
        //   }   
                 
        return response()->json([
            "data" => DoctorAppointmentResource::collection($doctor_query),
            'pagination' => [
                'total' => $doctor_query->total(),
                'per_page' => $doctor_query->perPage(),
                'current_page' => $doctor_query->currentPage(),
                'last_page' => $doctor_query->lastPage(),
                'from' => $doctor_query->firstItem(),
                'to' => $doctor_query->lastItem(),
            ],
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
