<?php

namespace Database\Seeders;

use App\Enums\Task\TaskPriority;
use App\Enums\Task\TaskStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Random\RandomException;

class DatabaseSeeder extends Seeder
{
    private int $total = 1000000;
    private int $chunkSize = 7000;

    /**
     * Seed the application's database.
     *
     * @return void
     * @throws RandomException
     */
    public function run(): void
    {
        $statuses   = TaskStatus::cases();
        $priorities = TaskPriority::cases();
        $categories = ['Работа', 'Дом', 'Личное', 'Учёба', 'Здоровье'];

        DB::statement('SET autocommit = 0');
        DB::statement('SET unique_checks = 0');
        DB::statement('SET foreign_key_checks = 0');


        DB::disableQueryLog();

        $now = now();
        $inserted = 0;
        $batch = [];

        for ($i = 1; $i <= $this->total; $i++) {
            $createDate = $now->copy()->subDays(random_int(0, 365));

            $batch[] = [
                'title'       => 'Task #' . $i . ' - ' . Str::random(12),
                'description' => 'Description for task ' . $i . ' ' . Str::random(80),
                'category'    => $categories[array_rand($categories)],
                'status'      => $statuses[array_rand($statuses)],
                'priority'    => $priorities[array_rand($priorities)],
                'create_date' => $createDate,
                'due_date'    => $createDate->copy()->addDays(random_int(1, 30)),
                'created_at'  => $now,
                'updated_at'  => $now,
            ];

            if (count($batch) >= $this->chunkSize) {
                DB::table('tasks')->insert($batch);
                $inserted += count($batch);
                $batch = [];

                DB::statement('COMMIT');

                $this->command?->getOutput()->write("\rВставлено: {$inserted} / {$this->total}");
            }
        }

        if (!empty($batch)) {
            DB::table('tasks')->insert($batch);
            $inserted += count($batch);
        }

        DB::statement('COMMIT');
        DB::statement('SET unique_checks = 1');
        DB::statement('SET foreign_key_checks = 1');
        DB::statement('SET autocommit = 1');

        $this->command?->getOutput()->writeln("\nГотово: {$inserted} задач создано.");
    }
}
