<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function quizzes()
{
    return $this->hasMany(Quiz::class);
}

public function results()
{
    return $this->hasMany(Result::class);
}
public function classesTeaching() {
    return $this->hasMany(Classroom::class,'teacher_id');
}

public function classes() {
    return $this->belongsToMany(Classroom::class,'classroom_user')->withTimestamps();
}
public function subjectsTeaching()
{
    return $this->hasMany(Subject::class,'teacher_id');
}

public function subjects()
{
    return $this->belongsToMany(Subject::class,'subject_user')->withTimestamps();
}
}
