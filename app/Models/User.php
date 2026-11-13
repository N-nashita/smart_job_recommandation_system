<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',

        // Shared role
        'role',

        // Candidate information
        'phone',
        'category',
        'experience',
        'degree',
        'institution',
        'major',
        'grad_year',
        'cgpa',

        // CV fields
        'summary',
        'location',
        'linkedin',
        'github',
        'portfolio',
        'languages',
        'certifications',
        'skills',
        'experiences',
        'projects',
        'cv_template',
        'educations',
        'photo',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'cgpa' => 'decimal:2',
            'skills' => 'array',
            'experiences' => 'array',
            'projects' => 'array',
            'educations' => 'array',
        ];
    }

    public function applications() { return $this->hasMany(Application::class, 'candidate_id'); }
    public function savedJobs() { return $this->belongsToMany(JobPost::class, 'saved_jobs', 'user_id', 'job_post_id')->withTimestamps(); }
    public function interviews() { return $this->hasManyThrough(Interview::class, Application::class, 'candidate_id', 'application_id'); }
    public function notifications() { return $this->hasMany(Notification::class); }
    public function scopeCandidates($q) { return $q->where('role', 'candidate'); }
    public function isCandidate(): bool { return $this->role === 'candidate'; }

    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo ? asset('storage/'.$this->photo) : null;
    }
}
