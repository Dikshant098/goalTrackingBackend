<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserDetail extends Model
{
    use HasFactory;
    protected $table = 'user_detail';
    protected $primaryKey = 'id';

    protected $fillable = [
        'short_detail',
        'long_detail',
        'mobile_number',
        'profile_image_url',
        'gender',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
