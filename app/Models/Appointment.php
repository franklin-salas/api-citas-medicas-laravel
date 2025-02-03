<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Wildside\Userstamps\Userstamps;

class Appointment extends Model
{
    use HasFactory,SoftDeletes,Userstamps;

    protected $fillable = [
        'date_appointment',
        'status',
        'last_name', 
        'specialty_id', 
        'doctor_id',     
        'patient_id',     
        'schedule_hour_id',
        'hour'     
    ];

    public function doctor(){
        return $this->belongsTo(User::class,"doctor_id","id");
    }

    public function patient(){
        return $this->belongsTo(Patient::class);
    }

    public function scheduleHour(){
        return $this->belongsTo(ScheduleHour::class);
    }
}
