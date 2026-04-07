<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaunchRegistration extends Model
{
    protected $fillable = [
        'name',
        'email',
        'sent_online_notification_at',
    ];

    protected $casts = [
        'sent_online_notification_at' => 'datetime',
    ];
}
