<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskCheckbox extends Model
{
    use HasFactory;

    public $table = 'task_checkbox';

    public $fillable = [
        'task_id',
        'is_done',
        'title',
    ];
}
