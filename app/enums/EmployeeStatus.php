<?php

namespace App\Enums;


enum EmployeeStatus: string
{
     case ACTIVE = 'active';
     case Resign = 'resign';
     case Terminated = 'terminated';
}
