<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'user_id',
        'project_id',
        'title',
        'priority',
        'status',
        'due_date',
        'completed_at',
        'kanban_col',
        'subtasks',
        'elapsed'
    ];

    protected $casts = [
        'due_date' => 'date',
        'completed_at' => 'datetime',
        'subtasks' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
