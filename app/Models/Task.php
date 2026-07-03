<?php

namespace App\Models;

use App\Enums\Task\TaskPriority;
use App\Enums\Task\TaskStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['title', 'description', 'status', 'priority', 'category', 'create_date', 'due_date'])]
class Task extends Model
{
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'create_date' => 'datetime',
            'due_date' => 'datetime',
            'status' => TaskStatus::class,
            'priority' => TaskPriority::class,
        ];
    }
}
