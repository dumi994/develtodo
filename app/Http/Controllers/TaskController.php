<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::where('user_id', auth()->id())
            ->with('project')
            ->orderBy('created_at', 'desc')
            ->get();

        $projects = \App\Models\Project::where('user_id', auth()->id())
            ->whereIn('status', ['in_arrivo', 'in_corso', 'in_revisione'])
            ->get();

        return view('tasks.index', compact('tasks', 'projects'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|max:255',
            'priority' => 'nullable|in:alta,media,bassa',
            'project_id' => 'nullable|exists:projects,id',
            'due_date' => 'nullable|date',
            'subtasks' => 'nullable|string',
        ]);

        $data['user_id'] = auth()->id();
        $data['priority'] ??= 'media';

        // Convert subtasks textarea to array
        if (!empty($data['subtasks'])) {
            $lines = explode("\n", $data['subtasks']);
            $subtasks = [];
            foreach ($lines as $line) {
                $line = trim($line);
                if (!empty($line)) {
                    $subtasks[] = ['title' => $line, 'done' => false];
                }
            }
            $data['subtasks'] = $subtasks;
        } else {
            $data['subtasks'] = [];
        }

        Task::create($data);

        return redirect()->route('tasks.index')->with('success', 'Task creato!');
    }

    public function update(Request $request, Task $task)
    {
        $this->authorize('update', $task);

        $data = $request->validate([
            'title' => 'required|max:255',
            'priority' => 'nullable|in:alta,media,bassa',
            'project_id' => 'nullable|exists:projects,id',
            'due_date' => 'nullable|date',
            'status' => 'nullable|in:da_fare,in_corso,fatto',
            'kanban_col' => 'nullable|string|max:20',
            'done' => 'nullable|boolean',
        ]);

        if (isset($data['done'])) {
            $data['status'] = $data['done'] ? 'fatto' : 'da_fare';
            $data['kanban_col'] = $data['done'] ? 'Fatto' : 'Da fare';
            $data['completed_at'] = $data['done'] ? now() : null;
        }

        $task->update($data);

        return redirect()->route('tasks.index')->with('success', 'Task aggiornato!');
    }

    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);
        $task->delete();

        return redirect()->route('tasks.index')->with('success', 'Task eliminato!');
    }

    public function updateTimer(Request $request, Task $task)
    {
        $this->authorize('update', $task);
        $task->update(['elapsed' => $request->input('elapsed', 0)]);
        return response()->json(['ok' => true]);
    }
}
