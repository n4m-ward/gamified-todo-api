<?php

namespace App\Dto;

class TaskDto extends Dto
{
    public ?int $id = null;
    public ?int $userId = null;
    public ?string $title = null;
    public ?string $description = null;
    public ?bool $expired = false;
    public ?string $dateOfTheTask = null;
    public ?string $conclusionDate = null;
    public ?bool $isDone = false;
    public ?int $qtyRepeats = 1;
    public ?bool $repeatForever = false;
    public ?int $taskTypeId = null;
    public ?int $originalTaskId = null;

    public array $checkbox = [];
    public array $meta = [];
}
