<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Goal extends Model
{
    use HasFactory;
    
    protected $fillable = ['user_id', 'title', 'description', 'status', 'start_date', 'end_date'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reminders()
    {
        return $this->hasMany(Reminder::class);
    }

    public function progress()
    {
        return $this->hasOne(GoalProgress::class);
    }
}
