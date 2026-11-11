<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecruitmentApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'mobile',
        'email',
        'role_applied',
        'subject_specialization',
        'experience_years',
        'resume_path',
        'status',
        'notes',
    ];
}
