<?php

namespace App\Dto\TaskDto;

use App\Dto\Dto;

class MetaDto extends Dto
{
    public ?int $id = null;
    public ?int $taskId = null;
    public ?int $hitTarget = null;
    public ?int $finalMeta = null;
    public ?string $title = null;
}
