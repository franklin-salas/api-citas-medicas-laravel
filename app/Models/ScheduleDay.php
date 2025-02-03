<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ScheduleDay extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'day',
      
    ];

   public function scheduleHours(){
        return $this->hasMany(ScheduleHour::class);
    }

    public function doctor(){
        return $this->belongsTo(User::class,"user_id", "id");
    }
}
