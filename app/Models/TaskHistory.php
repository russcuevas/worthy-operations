<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_task_id',
        'user_id',
        'action',
        'remarks',
    ];

    public function task()
    {
        return $this->belongsTo(StaffTask::class, 'staff_task_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
