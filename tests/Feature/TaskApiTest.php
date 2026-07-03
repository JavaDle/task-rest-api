<?php

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('can list tasks', function () {
    Task::factory(3)->create();

    $response = $this->getJson('/api/tasks');

    $response->assertOk()
        ->assertJsonCount(3, 'data');
});

test('can list tasks with pagination', function () {
    Task::factory(25)->create();

    $response = $this->getJson('/api/tasks?per_page=10');

    $response->assertOk()
        ->assertJsonCount(10, 'data')
        ->assertJsonStructure(['links']);
});

test('can paginate with cursor', function () {
    Task::factory(25)->create();

    $firstPage = $this->getJson('/api/tasks?per_page=10')->json();

    $this->assertArrayHasKey('links', $firstPage);
    $this->assertCount(10, $firstPage['data']);

    $nextCursor = $firstPage['links']['next'] ?? null;

    if ($nextCursor) {
        $cursor = parse_url($nextCursor, PHP_URL_QUERY);
        parse_str($cursor, $params);
        $cursorValue = $params['cursor'] ?? null;

        $this->assertNotNull($cursorValue);

        $secondPage = $this->getJson("/api/tasks?per_page=10&cursor=" . urlencode($cursorValue))->json();

        $this->assertCount(10, $secondPage['data']);
    }
});

test('can search tasks by title', function () {
    Task::factory()->create(['title' => 'Задача покупки']);
    Task::factory()->create(['title' => 'Задача работа']);
    Task::factory()->create(['title' => 'Другая задача']);

    $response = $this->getJson('/api/tasks?search=покупки');

    $response->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.title', 'Задача покупки');
});

test('can sort tasks by due_date', function () {
    Task::factory()->create(['due_date' => now()->addDays(3)]);
    Task::factory()->create(['due_date' => now()->addDays(1)]);
    Task::factory()->create(['due_date' => now()->addDays(2)]);

    $response = $this->getJson('/api/tasks?sort=due_date');

    $response->assertOk();
    $data = $response->json('data');
    $this->assertTrue(
        strtotime($data[0]['due_date']) <= strtotime($data[1]['due_date'])
        && strtotime($data[1]['due_date']) <= strtotime($data[2]['due_date'])
    );
});

test('can sort tasks by create_date', function () {
    Task::factory()->create(['create_date' => now()->addDays(3)]);
    Task::factory()->create(['create_date' => now()->addDays(1)]);
    Task::factory()->create(['create_date' => now()->addDays(2)]);

    $response = $this->getJson('/api/tasks?sort=create_date');

    $response->assertOk();
    $data = $response->json('data');
    $this->assertTrue(
        strtotime($data[0]['create_date']) <= strtotime($data[1]['create_date'])
        && strtotime($data[1]['create_date']) <= strtotime($data[2]['create_date'])
    );
});

test('returns 422 for invalid sort value', function () {
    Task::factory(3)->create();

    $response = $this->getJson('/api/tasks?sort=created_at');

    $response->assertUnprocessable()
        ->assertJsonValidationErrors('sort');
});

test('can create a task', function () {
    $payload = [
        'title' => 'Задача1',
        'description' => 'Задача1 описание',
        'create_date' => '2025-01-15T10:00:00',
        'due_date' => '2025-01-20T15:00:00',
        'priority' => 'high',
        'category' => 'Работа',
        'status' => 'pending',
    ];

    $response = $this->postJson('/api/tasks', $payload);

    $response->assertCreated()
        ->assertJsonStructure(['id', 'message']);

    $this->assertDatabaseHas('tasks', [
        'title' => 'Задача1',
        'priority' => 'high',
        'category' => 'Работа',
    ]);
});

test('validates required title on create', function () {
    $response = $this->postJson('/api/tasks', [
        'description' => 'Без названия',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors('title');
});

test('validates priority enum on create', function () {
    $response = $this->postJson('/api/tasks', [
        'title' => 'Тест',
        'priority' => 'invalid_priority',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors('priority');
});

test('validates status enum on create', function () {
    $response = $this->postJson('/api/tasks', [
        'title' => 'Тест',
        'status' => 'invalid_status',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors('status');
});

test('validates create_date on create', function () {
    $response = $this->postJson('/api/tasks', [
        'title' => 'Тест',
        'create_date' => 'not-a-date',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors('create_date');
});

test('validates title max length on create', function () {
    $response = $this->postJson('/api/tasks', [
        'title' => str_repeat('a', 256),
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors('title');
});

test('can show a single task', function () {
    $task = Task::factory()->create();

    $response = $this->getJson("/api/tasks/{$task->id}");

    $response->assertOk()
        ->assertJsonFragment([
            'id' => $task->id,
            'title' => $task->title,
        ]);
});

test('returns 404 for nonexistent task', function () {
    $response = $this->getJson('/api/tasks/9999');

    $response->assertNotFound();
});

test('can update a task', function () {
    $task = Task::factory()->create();

    $response = $this->putJson("/api/tasks/{$task->id}", [
        'title' => 'Обновленная задача',
        'create_date' => '2025-06-01T12:00:00',
        'status' => 'completed',
        'priority' => 'low',
    ]);

    $response->assertOk()
        ->assertJsonFragment(['message' => 'Task updated successfully']);

    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
        'title' => 'Обновленная задача',
        'status' => 'completed',
        'priority' => 'low',
    ]);
});

test('can partially update a task', function () {
    $task = Task::factory()->create(['title' => 'Оригинал']);

    $response = $this->patchJson("/api/tasks/{$task->id}", [
        'title' => 'Изменено',
    ]);

    $response->assertOk();

    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
        'title' => 'Изменено',
    ]);
});

test('returns 404 when updating nonexistent task', function () {
    $response = $this->putJson('/api/tasks/9999', [
        'title' => 'Тест',
    ]);

    $response->assertNotFound();
});

test('can delete a task', function () {
    $task = Task::factory()->create();

    $response = $this->deleteJson("/api/tasks/{$task->id}");

    $response->assertOk()
        ->assertJsonFragment(['message' => 'Task deleted successfully']);

    $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
});

test('returns 404 when deleting nonexistent task', function () {
    $response = $this->deleteJson('/api/tasks/9999');

    $response->assertNotFound();
});

test('returns empty list when no tasks exist', function () {
    $response = $this->getJson('/api/tasks');

    $response->assertOk()
        ->assertJsonCount(0, 'data');
});

test('search returns empty for no matches', function () {
    Task::factory()->create(['title' => 'Задача']);

    $response = $this->getJson('/api/tasks?search=несуществующее');

    $response->assertOk()
        ->assertJsonCount(0, 'data');
});
