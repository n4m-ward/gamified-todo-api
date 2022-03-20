<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskType extends Model
{
    use HasFactory;

    public const TASK_DIARIA = 1;
    public const TASK_SEMANAL = 2;
    public const TASK_MENSAL = 3;

    public const TASK_TYPE_DESCRIPTION = [
      self::TASK_DIARIA => 'Task Diaria',
      self::TASK_SEMANAL => 'Task Semanal',
      self::TASK_MENSAL => 'Task Mensal',
    ];

    public const DATE_INTERVAL_IN_DAYS = [
      self::TASK_DIARIA => 1,
      self::TASK_SEMANAL => 7,
      self::TASK_MENSAL => 30,
    ];

    public $table = 'task_type';
    public $fillable = [
      'id',
      'description',
    ];
}
