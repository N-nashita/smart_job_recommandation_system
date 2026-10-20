<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Recruiter extends Authenticatable 
{
    protected $fillable = ['name', 'job_title', 'email', 'phone', 'password', 'company_name',
    'registration_no', 'website', 'industry', 'hq_location', 'verification_status', 'verified_at'];

    protected $hidden = ['password'];

    protected function casts(): array
    {
        return ['password' => 'hashed', 'verified_at' => 'datetime'];   // password is hashed automatically
    }
}
