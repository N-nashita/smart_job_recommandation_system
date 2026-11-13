<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Application extends Model {
    protected $fillable = ['job_post_id', 'candidate_id', 'match_score', 'cv_template', 'status', 'skills_score', 'experience_score', 'location_score', 'summary'];
    public function job() { return $this->belongsTo(JobPost::class, 'job_post_id'); }
    public function candidate() { return $this->belongsTo(User::class, 'candidate_id'); }
    public function interviews() { return $this->hasMany(Interview::class); }
}
