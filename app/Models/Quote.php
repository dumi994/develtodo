<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    protected $fillable = [
        'user_id',
        'client',
        'description',
        'amount',
        'status',
        'file_path',
        'expiry_date'
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
