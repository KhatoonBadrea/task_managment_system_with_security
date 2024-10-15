<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TaskStatusUpdate;
use Illuminate\Http\Request;

class TaskStatusUpdateController extends Controller
{
    public function DialyReport()
    {
        $tasks = TaskStatusUpdate::whereDate('created_at', today())->get();
        // dd($task);
        return $tasks;
    }

  
}
