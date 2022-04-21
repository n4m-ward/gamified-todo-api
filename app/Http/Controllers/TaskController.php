<?php

namespace App\Http\Controllers;

use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function createTask(Request $request): JsonResponse
    {
        $taskDto = TaskService::makeTaskDto($request);

        return response()->json(TaskService::createTask($taskDto));
    }
}
