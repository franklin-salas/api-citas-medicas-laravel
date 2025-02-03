<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Wildside\Userstamps\Userstamps;

class Service extends Model
{
    use HasFactory,SoftDeletes,Userstamps;
    protected $fillable = [
        'name',
        'specialty_id',
        'price', 
        'status', 
        'description',                
    ];

    public function specialty(){
        return $this->belongsTo(Specialty::class);
    }
}
