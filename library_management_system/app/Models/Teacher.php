<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $fillable = [
        'employee_number',
        'user_id',
        'department_id'
    ];
}
