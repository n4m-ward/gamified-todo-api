<?php

namespace App\Services;

use App\Dto\TaskDto;
use App\Models\Task;
use App\Models\TaskCheckbox;
use App\Models\TaskMeta;
use App\Models\TaskType;
use App\Singleton\UserSingleton;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TaskService
{
    public const CARBON_FUNCTION_TO_GET_DIFF_DATE = [
        TaskType::TASK_MENSAL => 'diffInMonths',
        TaskType::TASK_SEMANAL => 'diffInWeeks',
        TaskType::TASK_DIARIA => 'diffInDays',
    ];

    public static function makeTaskDto(Request $request): TaskDto
    {
        $dto = new TaskDto();
        $dto->attachValues($request->except(['checkbox', 'meta']));
        $dto->userId = UserSingleton::get()->id;

        foreach ($request->get('meta', []) as $meta) {
            $metaDto = new TaskDto\MetaDto();
            $metaDto->attachValues($meta);
            $dto->meta[] = $metaDto;
        }
        foreach ($request->get('checkbox', []) as $checkbox) {
            $checkboxDto = new TaskDto\CheckboxDto();
            $checkboxDto->attachValues($checkbox);
            $dto->checkbox[] = $checkboxDto;
        }

        return $dto;
    }

    public static function createTask(TaskDto $taskDto): array
    {
        $arrayToReturn = [];
        $task = $taskDto->toArray(true);
        unset($task['checkbox']);
        unset($task['meta']);
        $taskAfterCreate = Task::create($task);
        $arrayToReturn['task'] = $taskAfterCreate;
        foreach ($taskDto->checkbox as  $checkbox) {
            $arrayToReturn['checkbox'][] = TaskCheckbox::create([
                'task_id' => $taskAfterCreate->id,
                'title' => $checkbox->title,
                'is_done' => $checkbox->isDone,
            ]);
        }
        foreach ($taskDto->meta as $meta) {
            $arrayToReturn['meta'][] = TaskMeta::create([
                'task_id' => $taskAfterCreate->id,
                'hit_target' => $meta->hitTarget,
                'final_meta' => $meta->finalMeta,
                'title' => $meta->title,
            ]);
        }

        return $arrayToReturn;
    }

    public function createAndDuplicateTask(TaskDto $taskDto): array
    {
        $firstTaskAreInserted = false;
        $arrayToReturn = null;

        $qtyRepeats = $this->getQtyRepeats($taskDto);
        while ($qtyRepeats >= 1) {
            if ($firstTaskAreInserted) {
                $taskDto->dateOfTheTask = $this->getDateOfTheFutureTask($taskDto);
                $taskDto->originalTaskId = $arrayToReturn['task']->id;
            }
            $result = $this->createTask($taskDto);
            if (!$firstTaskAreInserted) {
                $arrayToReturn = $result;
            }

            $qtyRepeats--;
            $firstTaskAreInserted = true;
        }

        return $arrayToReturn;
    }

    public function getQtyRepeats(TaskDto $taskDto): int
    {
        if (!$taskDto->repeatForever) {
            return $taskDto->qtyRepeats;
        }

        $dateOfTheDask = Carbon::createFromFormat('Y-m-d H:i:s', $taskDto->dateOfTheTask);
        $dateAfter3Months = Carbon::now()->addMonths(3);
        $carbonDiffMethod = self::CARBON_FUNCTION_TO_GET_DIFF_DATE[$taskDto->taskTypeId];

        return $dateAfter3Months->{$carbonDiffMethod}($dateOfTheDask);
    }

    public function getDateOfTheFutureTask(TaskDto $taskDto): string
    {
        $carbonMethodToArddDays = TaskType::CARBON_METHOD_TO_ADD_DAYS[$taskDto->taskTypeId];

        return Carbon::createFromFormat('Y-m-d H:i:s', $taskDto->dateOfTheTask)
            ->{$carbonMethodToArddDays}(1)
            ->format('Y-m-d H:i:s');
    }
}
