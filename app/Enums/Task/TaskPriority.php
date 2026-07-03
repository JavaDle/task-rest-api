<?php

namespace App\Enums\Task;

enum TaskPriority: string
{
    case Low = 'low';
    case Medium = 'medium';
    case High = 'high';

    public function label(): string
    {
        return match ($this) {
            self::Low => 'низкий',
            self::Medium => 'средний',
            self::High => 'высокий',
        };
    }
}
