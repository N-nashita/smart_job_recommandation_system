<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class FocusTask extends Model {
    protected $fillable = ['recruiter_id', 'title', 'note', 'done', 'for_date'];
    protected $casts = ['done' => 'boolean', 'for_date' => 'date'];
}
