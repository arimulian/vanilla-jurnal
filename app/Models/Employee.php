<?php

namespace App\Models;

use App\Enums\EmployeeStatus;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $guarded = false;
    protected $table = 'employees';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
        'hire_date' => 'datetime:Y-m-d',
        'status' => EmployeeStatus::class
    ];

    public function getSalaryAttribute($value)
    {
        return number_format($value, 2);
    }
}
