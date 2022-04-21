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

    public const CARBON_METHOD_TO_ADD_DAYS = [
      self::TASK_DIARIA => 'addDays',
      self::TASK_SEMANAL => 'addWeeks',
      self::TASK_MENSAL => 'addMonths',
    ];

    public $table = 'task_type';
    public $fillable = [
      'id',
      'description',
    ];
}
