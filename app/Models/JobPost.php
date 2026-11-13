<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class JobPost extends Model {
    protected $fillable = ['recruiter_id', 'title', 'status', 'location', 'type', 'salary_min', 'salary_max', 'description', 'skills', 'published_at', 'is_suspicious'];
    protected $casts = ['skills' => 'array', 'published_at' => 'datetime', 'is_suspicious' => 'boolean'];
    public function applications() { return $this->hasMany(Application::class); }
    public function recruiter() { return $this->belongsTo(Recruiter::class); }
    public function scopeOpen($q) { return $q->where('status', 'open'); }
    public function savedBy() { return $this->belongsToMany(User::class, 'saved_jobs', 'job_post_id', 'user_id')->withTimestamps(); }
}
