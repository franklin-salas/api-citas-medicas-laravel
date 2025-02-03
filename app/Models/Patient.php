<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Wildside\Userstamps\Userstamps;

class Patient extends Model
{
    use HasFactory ,SoftDeletes,Userstamps;

    protected $fillable = [
        'name',
        'email',
        'last_name', 
        'document', 
        'mobile',            
        'birth_date',
        'gender',
        'address',
        'antecedent_family',
        'antecedent_allergy',
        'avatar'
    ];
}
