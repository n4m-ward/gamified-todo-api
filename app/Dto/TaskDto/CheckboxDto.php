<?php

namespace App\Dto\TaskDto;

use App\Dto\Dto;

class CheckboxDto extends Dto
{
    public ?int $id = null;
    public ?int $taskId = null;
    public ?bool $isDone = null;
    public ?string $title = null;
}
