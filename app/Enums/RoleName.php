<?php

namespace App\Enums;

enum RoleName: string
{
    case ADMIN = 'admin';
    case ACADEMIC = 'academic';
    case TEACHER = 'teacher';
    case STUDENT = 'student';
}
