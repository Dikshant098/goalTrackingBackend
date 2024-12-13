<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
    use HasFactory;

    protected $fillable = ['goal_id', 'reminder_date', 'message'];

    public function goal()
    {
        return $this->belongsTo(Goal::class);
    }
}
