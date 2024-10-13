<?php

namespace App\Services;

use App\Models\Task;
use App\Models\Comment;
use Illuminate\Support\Facades\Log;


class CommentService
{
    public function addComment(array $data)
    {
        try {

        $task = Task::find($data['task_id']);
        // dd($task);
        if (!$task) {
            Log::error('Task not found with ID ' . $data['task_id']);
            return false;
        }
        $task->comments()->create([
            'comment' => $data['comment'],
        ]);
        return true;
        } catch (\Exception $e) {
            Log::error('Error in CommentService@addComment' . $e->getMessage());
            return false;
        }
    }
    public function updateComment(array $data, $commentId)
    {
        try {
            // البحث عن التعليق بواسطة ID
            $comment = Comment::find($commentId);
            if (!$comment) {
                Log::error('Comment not found with ID ' . $commentId);
                return false;
            }

            // تحديث محتوى التعليق
            $comment->update([
                'comment' => $data['comment'],
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Error in CommentService@updateComment: ' . $e->getMessage());
            return false;
        }
    }
    public function deleteComment($commentId)
    {
        try {
            // البحث عن التعليق بواسطة ID
            $comment = Comment::find($commentId);
            if (!$comment) {
                Log::error('Comment not found with ID ' . $commentId);
                return false;
            }

            // حذف التعليق
            $comment->delete();

            return true;
        } catch (\Exception $e) {
            Log::error('Error in CommentService@deleteComment: ' . $e->getMessage());
            return false;
        }
    }

    public function getComment($commentId)
{
    try {
        // البحث عن التعليق بواسطة ID
        $comment = Comment::select('comment')->find($commentId);
        if (!$comment) {
            Log::error('Comment not found with ID ' . $commentId);
            return null; // إرجاع null إذا لم يتم العثور على التعليق
        }

        return $comment;
    } catch (\Exception $e) {
        Log::error('Error in CommentService@getComment: ' . $e->getMessage());
        return null;
    }
}

}
