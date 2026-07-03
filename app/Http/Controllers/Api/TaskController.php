<?php

namespace App\Http\Controllers\Api;

use App\Concerns\TaskControllerActionsInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Task\TaskIndexRequest;
use App\Http\Requests\Task\TaskStoreRequest;
use App\Http\Requests\Task\TaskUpdateRequest;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class TaskController extends Controller
{
    public function __construct(
        private readonly TaskControllerActionsInterface $taskAction,
    ) {}

    /**
     * @param TaskIndexRequest $request
     * @return ResourceCollection
     */
    public function index(TaskIndexRequest $request): ResourceCollection
    {
        return $this->taskAction->index($request);
    }

    /**
     * @param Task $task
     * @return JsonResource
     */
    public function show(Task $task): JsonResource
    {
        return $this->taskAction->show($task);
    }

    /**
     * @param TaskStoreRequest $request
     * @return JsonResponse
     */
    public function store(TaskStoreRequest $request): JsonResponse
    {
        return $this->taskAction->store($request);
    }

    /**
     * @param TaskUpdateRequest $request
     * @param Task $task
     * @return JsonResponse
     */
    public function update(TaskUpdateRequest $request, Task $task): JsonResponse
    {
        return $this->taskAction->update($request, $task);
    }

    /**
     * @param Task $task
     * @return JsonResponse
     */
    public function destroy(Task $task): JsonResponse
    {
        return $this->taskAction->destroy($task);
    }
}
