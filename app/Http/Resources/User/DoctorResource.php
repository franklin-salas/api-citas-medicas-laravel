<?php

namespace App\Http\Resources\user;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DoctorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    { 

        $schedule_day_hours =  collect([]);


        foreach($this->resource->scheduleDays as $scheduleDay){
            
            foreach($scheduleDay->scheduleHours as $schedulehour){
              $hour = $schedulehour->doctorScheduleHour;

              $schedule_day_hours->push(
                [
                    "id" =>$hour->id,
                    "day" => $scheduleDay->day,
                    "hour_start" => Carbon::parse(date("Y-m-d").' '.$hour->hour_start)->format("h:i A"),
                    "hour_end" => Carbon::parse(date("Y-m-d").' '.$hour->hour_end)->format("h:i A"),
                    "hour" => Carbon::parse(date("Y-m-d").' '.$hour->hour.":00:00")->format("h:i A"),
                ]
                );
            }
        }

        return [
        "id" => $this->resource->id,
        "name" => $this->resource->name,
        "last_name" => $this->resource->last_name,
        "document" => $this->resource->document? $this->resource->document: '' ,
        "email" => $this->resource->email,
        "birth_date" => $this->resource->birth_date ? Carbon::parse($this->resource->birth_date)->format("d/m/Y") : '',
        "gender" => $this->resource->gender,
        "education" => $this->resource->education,
        "designation" => $this->resource->designation,
        "address" => $this->resource->address,
        "mobile" => $this->resource->mobile,
        "created_at" => $this->resource->created_at->format("Y/m/d"),
        "role_id" => $this->resource->roles->first()->id,
        "specialty_id" =>$this->resource->specialty_id,
        "url_avatar" =>  $this->resource->avatar? env("APP_URL")."/storage/".$this->resource->avatar: env("APP_URL")."/storage/img/user.jpg",
        "schedule_day_hours" => $schedule_day_hours,
        ];
    }
}
