<?php

namespace App\Enums\Task;

enum TaskStatus: string
{
    case Pending = 'pending';
    case Completed = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'не выполнена',
            self::Completed => 'выполнена',
        };
    }
}
