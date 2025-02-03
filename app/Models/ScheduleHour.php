<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

class ScheduleHour extends Model
{
    use HasFactory ;
    protected $fillable = [
       'schedule_day_id',
        'doctor_schedule_id',
      
    ];

    public function scheduleDay(){
        return $this->belongsTo(scheduleDay::class);
    }

    public function doctorScheduleHour(){
        return $this->belongsTo(DoctorSchedule::class,'doctor_schedule_id','id');
    }


}
