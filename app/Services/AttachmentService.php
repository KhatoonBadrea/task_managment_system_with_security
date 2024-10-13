<?php

namespace App\Services;

use App\Models\Task;

class AttachmentService
{
    public function addAttach(array $data)
    {
        $task = Task::find($data['task_id']);
        $task->attachments()->create([
            'file_path' => $data['path'],
            'file_type' => $data['type'],
        ]);
        
    }
}
