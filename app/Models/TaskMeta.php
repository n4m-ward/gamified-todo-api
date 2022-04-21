<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskMeta extends Model
{
    use HasFactory;

    public $table = 'task_meta';

    public $fillable = [
        'task_id',
        'hit_target',
        'final_meta',
        'title',
    ];
}
