<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\TaskComment;

class TaskCommentController extends Controller
{
    public function store(Request $request, Task $task)
    {
        $request->validate(['body' => 'required|string|max:1000']);

        $task->comments()->create([
            'user_id' => auth()->id(),
            'body' => $request->body,
        ]);

        return back()->with('success', 'Seguimiento añadido correctamente.');
    }
}
