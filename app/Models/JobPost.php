<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class JobPost extends Model {
    protected $fillable = ['recruiter_id', 'title', 'status', 'location', 'type', 'salary_min', 'salary_max', 'description', 'skills', 'published_at'];
    protected $casts = ['skills' => 'array', 'published_at' => 'datetime'];
    public function applications() { return $this->hasMany(Application::class); }
}
