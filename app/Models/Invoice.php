<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'user_id',
        'quote_id',
        'number',
        'client',
        'amount',
        'status',
        'due_date',
        'sent_at',
        'paid_at'
    ];

    protected $casts = [
        'due_date' => 'date',
        'sent_at' => 'date',
        'paid_at' => 'date',
        'amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function quote()
    {
        return $this->belongsTo(Quote::class);
    }
}
