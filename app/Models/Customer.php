<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'customers';
    protected $guarded = false;
    protected $dateFormat = 'Y-m-d H:i:s';
}
