<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    protected $fillable = ['name','code','teacher_id','description'];

    public function teacher() {
        return $this->belongsTo(User::class,'teacher_id');
    }

    public function students() {
        return $this->belongsToMany(User::class,'classroom_user')->withTimestamps();
    }

    public function quizzes() {
        return $this->hasMany(Quiz::class);
    }
}
