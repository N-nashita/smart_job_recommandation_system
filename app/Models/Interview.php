<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Interview extends Model {
    protected $fillable = ['application_id', 'recruiter_id', 'scheduled_at', 'round', 'mode', 'status'];
    protected $casts = ['scheduled_at' => 'datetime'];
    public function application() { return $this->belongsTo(Application::class); }
}
