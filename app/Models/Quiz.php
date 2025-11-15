<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function results()
    {
        return $this->hasMany(Result::class);
    }
    public function subject()
{
    return $this->belongsTo(Subject::class);
}

public function teacher()
{
    return $this->belongsTo(User::class,'user_id');
}

/** Deadline helper */
public function status()
{
    if (!$this->deadline) return 'open';
    $now = now();
    if ($now->greaterThan($this->deadline)) return 'closed';
    // upcoming = within 1 day? (optional)
    if ($now->greaterThan($this->deadline->subDay())) return 'upcoming';
    return 'open';
}
protected $casts = [
  'deadline' => 'datetime',
];

}
