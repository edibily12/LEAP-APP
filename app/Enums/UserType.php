<?php

namespace App\Enums;

enum UserType: string
{
    case ADMIN = 'admin';
    case STAFF = 'staff';
    case STUDENT = 'student';

}
