<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    public $table = 'task';

    public $fillable = [
      'user_id',
      'description',
      'expired',
      'date_of_the_task',
      'conclusion_date',
      'is_done',
      'qty_repeats',
      'repeat_forever',
      'task_type_id',
      'original_task_id',
    ];
}
