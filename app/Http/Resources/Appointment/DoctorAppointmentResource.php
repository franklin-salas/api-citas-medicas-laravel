<?php

namespace App\Http\Resources\Appointment;

use App\Models\Appointment;
use App\Models\DoctorSchedule;
use App\Models\ScheduleHour;
use App\Models\Specialty;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DoctorAppointmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        

        $date_appointment = $request->date_appointment;
        $hour = $request->hour_id;
        $specialty_id = $request->specialty_id;


$appointments = Appointment::with('scheduleHour')
    ->where('doctor_id', $this->resource->id)
    ->whereDate('date_appointment',Carbon::parse($date_appointment)->format("Y-m-d"))
    ->where('hour', $hour)
    ->where('specialty_id', $specialty_id)
    ->whereIn('status', ['ACTIVO', 'PAGADO'])
    ->get();

    $specialty = Specialty::where('id', $specialty_id)->first(); // Busca el doctor con ID 5


$work_schedule = DoctorSchedule::where('hour', $hour)
    ->select('id', 'hour_start', 'hour_end', 'hour')
    ->get();


$occupiedScheduleIds = $appointments->pluck('scheduleHour.doctor_schedule_id')->toArray();

$work_schedule->each(function ($schedule) use ($occupiedScheduleIds) {
    $schedule->avalible = in_array($schedule->id, $occupiedScheduleIds) ? false : true;
    $schedule->hour_start = Carbon::parse(date("Y-m-d").' '.$schedule->hour_start)->format("h:i A");
    $schedule->hour_end = Carbon::parse(date("Y-m-d").' '.$schedule->hour_end)->format("h:i A");
});


return [
    "id" => $this->resource->id,
    "avatar" =>  $this->resource->avatar? env("APP_URL")."/storage/".$this->resource->avatar: env("APP_URL")."/storage/img/user.jpg",
    "full_name" => $this->resource->full_name,
    "avalible" => $this->resource->avalible - $appointments->count(),
    "specialty_id" => $specialty->id,
    "specialty_name" => $specialty->name,
    "schedule" => $work_schedule
];

        /*$date_appointment =$request->date_appointment;
        $hour = $request->hour;
        $specialty_id= $request->specialty_id;

        $appointments = Appointment::where('doctor_id', $this->resource->user_id)
            ->whereDate('date_appointment',$date_appointment)
            ->where('hour',$hour)
            ->where('specialty_id',$specialty_id)
            ->whereIn('status',['ACTIVO','PAGADO'])->get();

        $work_schedule = DoctorSchedule::where('hour',$hour)
                        ->select('id' ,'hour_start' ,'hour_end', 'hour')
                        ->get();

        foreach ($work_schedule as  $schedule) {
            $schedule->status = "DISPONIBLE";
            foreach ($appointments as $appointment) {
               
                $scheduleHour = ScheduleHour::find($appointment->schedule_hour_id)->doctorScheduleHour()->first();
               
                    if($schedule->id == $scheduleHour->id){
                        $schedule->status = "OCUPADO";
                     }
                
            }
        }
         

        return [
            "id" => $this->resource->user_id,
            "full_name" => $this->resource->user_full_name,
            "avalible" => $this->resource->avalible - $appointments->count(),
            "schedule" => $work_schedule
        ];*/
    }
}
