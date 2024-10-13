<?php

namespace App\Services;

use App\Models\Task;
use Illuminate\Support\Arr;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\TaskResource;
use App\Http\Traits\ApiResponseTrait;
use App\Events\TaskStatusUpdatedEvent;

class TaskService
{

    use ApiResponseTrait;

    //===================================================getAllTask==========================

    /**
     * fetch the all task from DB and fillter it
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllTask($priority = null, $status = null, $type = null, $due_date = null, $assignedUser = null, $dependentTaskName = null)
    {
        try {
            $query = Task::query();

            if ($priority) {
                $query->priority($priority);
            }

            if ($status) {
                $query->status($status);
            }
            if ($type) {
                $query->type($type);
            }
            if ($due_date) {
                $query->due_date($due_date);
            }
            if ($assignedUser) {
                $query->assignedToUser($assignedUser);
            }
            if ($dependentTaskName) {
                $query->dependsOnTask($dependentTaskName);
            }

            $tasks = $query->get();

            return TaskResource::collection($tasks);
        } catch (\Exception $e) {
            Log::error('Error in TaskService@getAllTask: ' . $e->getMessage());
            return false;
        }
    }




    //===================================================create_task==========================

    /**
     * create new task
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function create_task($data)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $status = is_null(Arr::get($data, 'depends_on')) ? 'Open' : 'Blocked';

        try {

            $task = Task::create([
                'title' => $data['title'],
                'description' => $data['description'],
                'type' => $data['type'],
                'status' => $status,
                'priority' => $data['priority'],
                'due_date' => null,
                'created_by' => $user->id,
                'assigned_to' => $data['assigned_to'],
                'depends_on' => Arr::get($data, 'depends_on'),
            ]);
          
            return true;
                } catch (\Exception $e) {
            Log::error('Error in TaskService@create_Task: ' . $e->getMessage() . $e->getLine());
            return false;
        }
    }

    //===================================================update_task==========================

    /**
     * update the task
     * @param Task $task
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function update_task(Task $task, array $data)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            if (!$task->exists) {
                return $this->notFound('Task not found.');
            }
            //   Update only the fields that are provided in the data array
            $task->update(array_filter([
                'title' => $data['title'] ?? $task->title,
                'description' => $data['description'] ?? $task->description,
                'priority' => $data['priority'] ?? $task->priority,
                'due_date' => $data['due_date'] ?? $task->due_date,
                'status' => $data['status'] ?? $task->status,
                'assigned_to' => $data['assigned_to'] ?? $task->assigned_to,
                'created_by' => $user->id ?? $task->created_by,
                'type' => $data['type'] ?? $task->type,
                'depends_on' => $data['depends_on'] ?? $task->depends_on,
            ]));

            return true;          
        } catch (\Exception $e) {
            Log::error('Error in TaskService@update_Task' . $e->getMessage());
            return false;
        }
    }



    //===================================================update_assigned_to==========================

    /**
     * update assigned to in the task
     * @param Task $task
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     */

    public function update_assigned_to(Task $task, array $data)
    {
        try {

            if (!$task->exists) {
                return $this->notFound('Task not found.');
            }
            //   Update only the fields that are provided in the data array
            $task->update(array_filter([
                'due_date' => null,
                'assigned_to' => $data['assigned_to'] ?? $task->assigned_to,
            ]));


            // Return the updated task as a resource
            return TaskResource::make($task)->toArray(request());
        } catch (\Exception $e) {
            Log::error('Error in TaskService@update_Task' . $e->getMessage());
            return $this->errorResponse('An error occurred: ' . 'there is an error in the server', [], 500);
        }
    }


    //===================================================get_assigned_task_for_user==========================

    /**
     * get all task that assigned to the one user
     * @return \Illuminate\Http\JsonResponse
     */
    public function get_assigned_task_for_user()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            $task = Task::where('assigned_to', $user->id)->get();

            return TaskResource::collection($task);
        } catch (\Exception $e) {
            Log::error('Error in TaskService@get_assigned_task_for_user: ' . $e->getMessage());
            return $this->errorResponse('An error occurred: ' . 'there is an error in the server', 500);
        }
    }

    //===================================================show_task ==========================

    /**
     * show the specific task
     * @param Task $task
     * @return \Illuminate\Http\JsonResponse
     */

    public function show_task(Task $task)
    {
        try {

            if (!$task->exists) {
                return $this->notFound('Task not found.');
            }

            return TaskResource::make($task)->toArray(request());
        } catch (\Exception $e) {
            Log::error('Error in TaskService@show_task: ' . $e->getMessage());
            return $this->errorResponse('An error occurred: ' . 'there is an error in the server', 500);
        }
    }


    public function update_status(Task $task, array $data)
    {
        try {
            $task->status = $data['status'] ?? $task->status;
            $task->save();
    
            event(new TaskStatusUpdatedEvent($task));
    
            return $task;
        } catch (\Exception $e) {
            Log::error('Error in TaskService@update_status: ' . $e->getMessage());
            return $this->errorResponse('An error occurred: there is an error in the server', 500);
        }
    }
    
}
