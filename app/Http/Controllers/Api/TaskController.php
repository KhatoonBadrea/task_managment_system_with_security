<?php

namespace App\Http\Controllers\Api;

use App\Models\Task;
use Illuminate\Http\Request;
use App\Services\TaskService;
use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
// use App\Http\Requests\updateStatusRequest;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Http\Requests\Task\updateStatusRequest;
use App\Http\Requests\Task\updateAssignedRequest;

class TaskController extends Controller
{
    use ApiResponseTrait;
    protected $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $priority = $request->input('priority');
        $status = $request->input('status');
        $type = $request->input('type');
        $due_date = $request->input('due_date');
        $assignedUser = $request->input('assignedUser');
        $dependentTaskName = $request->input('dependentTaskName');
        $response = $this->taskService->getAllTask($priority, $status, $type, $due_date, $assignedUser, $dependentTaskName);

        // dd($response);
        // if (!$response) {
        //     return $this->error();
        // } else {
        return $this->successResponse('this is all tasks', $response, 200);
        // }
    }

    /**
     * Store a newly created resource in storage.
     * @param StoreTaskRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreTaskRequest $request)
    {
        $this->authorize('create', Task::class);
        $validationdata = $request->validated();
        $response = $this->taskService->create_task($validationdata);
        if (!$response) {
            return $this->error();
        } else {
            return $this->success();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        $this->authorize('update', $task);
        $validationdata = $request->validated();
        $response = $this->taskService->update_task($task, $validationdata);
        if (!$response) {
            return $this->error();
        } else {
            return $this->success();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        // $this->authorize('delete', $task);

        $task->delete();
        return $this->success();
    }


    /**
     * update the assigned to for the specific task
     * @param updateAssignedRequest $request
     * @param Task $task
     * @return \Illuminate\Http\JsonResponse
     */
    public function update_assigned_to(updateAssignedRequest $request, Task $task)
    {
        // $this->authorize('update', $task);

        $validatedRequest = $request->validated();
        $newAssigne = $this->taskService->update_assigned_to($task, $validatedRequest);
        return $this->successResponse($newAssigne, 'Assigned_to updated successfully.', 200);
    }

     /**
     * user can update the status for the task that assigned to him
     * @param updateStatusRequest $request
     * @param Task $task
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus(updateStatusRequest $request, Task $task)
    {
        $validatedRequest = $request->validated();

        $newTask = $this->taskService->update_status($task, $validatedRequest);

        return $this->successResponse($newTask, 'Task updated successfully.', 200);
    }
}
