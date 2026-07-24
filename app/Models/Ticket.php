<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'user_id',
        'client',
        'subject',
        'priority',
        'category',
        'status',
        'notes',
        'last_update'
    ];

    protected $casts = [
        'last_update' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
