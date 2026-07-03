<?php

namespace App\Contracts;

use App\Http\Requests\Task\TaskIndexRequest;
use App\Http\Requests\Task\TaskStoreRequest;
use App\Http\Requests\Task\TaskUpdateRequest;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

interface TaskControllerActionsInterface
{
    public function index(TaskIndexRequest $request): ResourceCollection;

    public function show(Task $task): JsonResource;

    public function store(TaskStoreRequest $request): JsonResponse;

    public function update(TaskUpdateRequest $request, Task $task): JsonResponse;

    public function destroy(Task $task): JsonResponse;
}
