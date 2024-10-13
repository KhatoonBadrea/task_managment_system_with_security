<?php

namespace App\Http\Controllers\Api;

use App\Models\Task;
use App\Models\Attachment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AtachmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Task $task)
    {
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');

            $path = $file->store('attachments', 'public');

            $task->attachments()->create([
                'file_path' => $path
            ]);

            return response()->json(['message' => 'Attachment uploaded successfully!', 'path' => $path], 201);
        }

        return response()->json(['error' => 'Please upload a file.'], 400);
    }


    public function destroy($id)
    {
        $attachment = Attachment::find($id);
        $attachment->delete();

        return response()->json(['message' => 'Attachment deleted successfully']);
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
    public function update(Request $request, string $id)
    {
        //
    }

    
}
