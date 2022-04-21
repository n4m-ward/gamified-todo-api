<?php

namespace Tests\Unit\Service;

use App\Dto\TaskDto;
use App\Models\Task;
use App\Models\TaskType;
use App\Models\User;
use App\Services\TaskService;
use App\Singleton\UserSingleton;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TaskServiceUnitTest extends TestCase
{
    use DatabaseTransactions;

    private TaskService $taskService;
    private TaskDto $taskDto;

    protected function setUp(): void
    {
        parent::setUp();
        $this->taskService = new TaskService();
        $this->taskDto = new TaskDto();
        $dateNow = '2022-03-27';
        $dateNow = Carbon::createFromFormat('Y-m-d', $dateNow);
        Carbon::setTestNow($dateNow);
    }

    public function testMakeTaskDtoWorks(): void
    {
        $paramsToIgnore = ['meta', 'checkbox'];
        $user = User::factory()->create();
        UserSingleton::set($user);
        $dtoParams = [
            'title' => 'test title',
            'description' => 'description test',
            'dateOfTheTask' => Carbon::now()->format('Y-m-d H:i:s'),
            'conclusionDate' => Carbon::now()->format('Y-m-d H:i:s'),
            'isDone' => false,
            'qtyRepeats' => 5,
            'repeatForever' => false,
            'taskTypeId' => TaskType::TASK_MENSAL,
            'checkbox' => [
                [
                    'isDone' => true,
                    'title' => 'checkbox 1',
                ],
                [
                    'isDone' => true,
                    'title' => 'checkbox 2',
                ],
            ],
            'meta' => [
                [
                    'hitTarget' => 23,
                    'finalMeta' => 100,
                    'title' => 'meta 1'
                ],
                [
                    'hitTarget' => 45,
                    'finalMeta' => 100,
                    'title' => 'meta 2'
                ],
            ]
        ];
        $request = $this->getFakeRequest($dtoParams, 'POST');
        $dtoResult = $this->taskService::makeTaskDto($request);
        $this->assertEquals($user->id, $dtoResult->userId);

        foreach ($dtoParams as $key => $param) {
            if(in_array($key, $paramsToIgnore)) {
                continue;
            }
            $this->assertEquals($param, $dtoResult->{$key});
        }

        $this->assertCount(2, $dtoResult->checkbox);
        $this->assertCount(2, $dtoResult->meta);

        foreach ($dtoResult->meta as $meta) {
            $this->assertInstanceOf(TaskDto\MetaDto::class, $meta);
        }
        foreach ($dtoResult->checkbox as $checkbox) {
            $this->assertInstanceOf(TaskDto\CheckboxDto::class, $checkbox);
        }
    }

    public function testCreateTaskWorks(): void
    {
        $user = User::factory()->create();
        UserSingleton::set($user);
        $dtoParams = [
            'title' => 'test title',
            'description' => 'description test',
            'dateOfTheTask' => Carbon::now()->format('Y-m-d H:i:s'),
            'conclusionDate' => Carbon::now()->format('Y-m-d H:i:s'),
            'isDone' => false,
            'qtyRepeats' => 5,
            'repeatForever' => false,
            'taskTypeId' => TaskType::TASK_MENSAL,
            'checkbox' => [
                [
                    'isDone' => true,
                    'title' => 'checkbox 1',
                ],
                [
                    'isDone' => true,
                    'title' => 'checkbox 2',
                ],
            ],
            'meta' => [
                [
                    'hitTarget' => 23,
                    'finalMeta' => 100,
                    'title' => 'meta 1'
                ],
                [
                    'hitTarget' => 45,
                    'finalMeta' => 100,
                    'title' => 'meta 2'
                ],
            ]
        ];
        $request = $this->getFakeRequest($dtoParams, 'POST');
        $dtoResult = $this->taskService::makeTaskDto($request);

        $result = $this->taskService->createTask($dtoResult);
        $dtoResult->title = $result['task']->title;
        $dtoResult->description = $result['task']->description;
        $dtoResult->dateOfTheTask = $result['task']->date_of_the_task;
        $dtoResult->conclusionDate = $result['task']->conclusion_date;
        $dtoResult->qtyRepeats = $result['task']->qty_repeats;
        $dtoResult->repeatForever = $result['task']->repeat_forever;
        $dtoResult->taskTypeId = $result['task']->task_type_id;

        $this->assertCount(2, $result['checkbox']);
        $this->assertCount(2, $result['meta']);
    }

    public function testCreateAndDuplicateTaskWorks(): void
    {
        $this->taskDto->title  = 'titleTest';
        $this->taskDto->taskTypeId = TaskType::TASK_DIARIA;
        $this->taskDto->qtyRepeats = 5;
        $this->taskDto->userId = User::factory()->create()->id;
        $this->taskDto->dateOfTheTask = Carbon::now()->format('Y-m-d H:i:s');
        $result = $this->taskService->createAndDuplicateTask($this->taskDto);

        $this->assertNotNull($result);
        $duplicatedTasks = Task::query()->where('original_task_id', $result['task']->id)->get();

        $this->assertCount(4, $duplicatedTasks);
    }

    public function testCreateAndDuplicateTaskRepeat3TimesTOInfinityMensalTasks(): void
    {
        $this->taskDto->title  = 'titleTest';
        $this->taskDto->taskTypeId = TaskType::TASK_MENSAL;
        $this->taskDto->qtyRepeats = 1;
        $this->taskDto->repeatForever = true;
        $this->taskDto->userId = User::factory()->create()->id;
        $this->taskDto->dateOfTheTask = Carbon::now()->format('Y-m-d H:i:s');
        $this->taskService->createAndDuplicateTask($this->taskDto);
        $result = Task::query()
            ->where('user_id', $this->taskDto->userId)
            ->where('title', $this->taskDto->title)
            ->get();

        $this->assertCount(3, $result);
    }

    public function testCreateAndDuplicateTaskRepeat13TimesTOInfinityWeeksTasks(): void
    {
        $this->taskDto->title  = 'titleTest';
        $this->taskDto->taskTypeId = TaskType::TASK_SEMANAL;
        $this->taskDto->qtyRepeats = 1;
        $this->taskDto->repeatForever = true;
        $this->taskDto->userId = User::factory()->create()->id;
        $this->taskDto->dateOfTheTask = Carbon::now()->format('Y-m-d H:i:s');
        $this->taskService->createAndDuplicateTask($this->taskDto);
        $result = Task::query()
            ->where('user_id', $this->taskDto->userId)
            ->where('title', $this->taskDto->title)
            ->get();

        $this->assertCount(13, $result);
    }

    public function testCreateAndDuplicateTaskRepeat92TimesTOInfinityDailyTasks(): void
    {
        $this->taskDto->title  = 'titleTest';
        $this->taskDto->taskTypeId = TaskType::TASK_DIARIA;
        $this->taskDto->qtyRepeats = 1;
        $this->taskDto->repeatForever = true;
        $this->taskDto->userId = User::factory()->create()->id;
        $this->taskDto->dateOfTheTask = Carbon::now()->format('Y-m-d H:i:s');
        $this->taskService->createAndDuplicateTask($this->taskDto);
        $result = Task::query()
            ->where('user_id', $this->taskDto->userId)
            ->where('title', $this->taskDto->title)
            ->get();

        $this->assertCount(92, $result);
    }

    public function testCreateAndDuplicateTaskRepeat2TimesTOInfinityMensalTasksThatBeginNextMorning(): void
    {
        $this->taskDto->title  = 'titleTest';
        $this->taskDto->taskTypeId = TaskType::TASK_MENSAL;
        $this->taskDto->qtyRepeats = 1;
        $this->taskDto->repeatForever = true;
        $this->taskDto->userId = User::factory()->create()->id;
        $this->taskDto->dateOfTheTask = Carbon::now()->addMonth()->format('Y-m-d H:i:s');
        $this->taskService->createAndDuplicateTask($this->taskDto);
        $result = Task::query()
            ->where('user_id', $this->taskDto->userId)
            ->where('title', $this->taskDto->title)
            ->get();

        $this->assertCount(2, $result);
    }

    public function testCreateAndDuplicateTaskRepeat11TimesTOInfinityWeeksTasksThatBeginInTwoWeeks(): void
    {
        $this->taskDto->title  = 'titleTest';
        $this->taskDto->taskTypeId = TaskType::TASK_SEMANAL;
        $this->taskDto->repeatForever = true;
        $this->taskDto->userId = User::factory()->create()->id;
        $this->taskDto->dateOfTheTask = Carbon::now()->addWeeks(2)->format('Y-m-d H:i:s');
        $this->taskService->createAndDuplicateTask($this->taskDto);
        $result = Task::query()
            ->where('user_id', $this->taskDto->userId)
            ->where('title', $this->taskDto->title)
            ->get();

        $this->assertCount(11, $result);
    }

    public function testCreateAndDuplicateTaskRepeat70TimesTOInfinityDailyTasksThatBeginIn22Days(): void
    {
        $this->taskDto->title  = 'titleTest';
        $this->taskDto->taskTypeId = TaskType::TASK_DIARIA;
        $this->taskDto->qtyRepeats = 1;
        $this->taskDto->repeatForever = true;
        $this->taskDto->userId = User::factory()->create()->id;
        $this->taskDto->dateOfTheTask = Carbon::now()->addDays(22)->format('Y-m-d H:i:s');
        $this->taskService->createAndDuplicateTask($this->taskDto);
        $result = Task::query()
            ->where('user_id', $this->taskDto->userId)
            ->where('title', $this->taskDto->title)
            ->get();

        $this->assertCount(70, $result);
    }
}
