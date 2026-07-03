<?php

namespace Database\Factories;

use App\Enums\Task\TaskPriority;
use App\Enums\Task\TaskStatus;
use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'description' => fake()->optional(0.7)->paragraph(),
            'status' => fake()->randomElement(TaskStatus::cases()),
            'priority' => fake()->randomElement(TaskPriority::cases()),
            'category' => fake()->optional(0.8)->randomElement(['Работа', 'Дом', 'Личное', 'Учёба', 'Здоровье']),
            'create_date' => fake()->dateTimeBetween('-1 month', 'now'),
            'due_date' => fake()->dateTimeBetween('+1 week', '+3 months'),
        ];
    }
}
