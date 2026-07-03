<?php

namespace App\Actions;

use App\Contracts\TaskControllerActionsInterface;
use App\Http\Requests\Task\TaskIndexRequest;
use App\Http\Requests\Task\TaskStoreRequest;
use App\Http\Requests\Task\TaskUpdateRequest;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Throwable;

class TaskControllerActions implements TaskControllerActionsInterface
{
    /**
     * @param TaskIndexRequest $request
     * @return ResourceCollection
     * @throws Throwable
     */
    public function index(TaskIndexRequest $request): ResourceCollection
    {
        $query = Task::query()
            ->when($request->input('search'), function ($q, $search) {
                $q->whereLike('title', "%{$search}%")
                    ->orWhereLike('description', "%{$search}%");
            })
            ->when($request->input('sort'), fn($q, $sort) => $q->orderBy($sort));

        $perPage = $request->input('per_page', 15);
        $cursor = $request->input('cursor');

        return $query->cursorPaginate($perPage, ['*'], 'cursor', $cursor)->toResourceCollection();
    }

    /**
     * @param Task $task
     * @return JsonResource
     */
    public function show(Task $task): JsonResource
    {
        return $task->toResource();
    }

    /**
     * @param TaskStoreRequest $request
     * @return JsonResponse
     */
    public function store(TaskStoreRequest $request): JsonResponse
    {
        $task = Task::create($request->validated());

        return response()->json([
            'id' => $task->id,
            'message' => 'Task created successfully',
        ], 201);
    }

    /**
     * @param TaskUpdateRequest $request
     * @param Task $task
     * @return JsonResponse
     */
    public function update(TaskUpdateRequest $request, Task $task): JsonResponse
    {
        $task->update($request->validated());

        return response()->json([
            'message' => 'Task updated successfully',
        ]);
    }

    /**
     * @param Task $task
     * @return JsonResponse
     */
    public function destroy(Task $task): JsonResponse
    {
        $task->delete();

        return response()->json([
            'message' => 'Task deleted successfully',
        ]);
    }
}
