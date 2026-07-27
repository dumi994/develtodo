<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $orderMap = [
            'in_corso' => 1,
            'in_revisione' => 2,
            'in_arrivo' => 3,
            'completato' => 4,
            'archiviato' => 5,
        ];

        $projects = Project::where('user_id', auth()->id())
            ->get()
            ->sortBy(function ($p) use ($orderMap) {
                return $orderMap[$p->status] ?? 99;
            })
            ->sortBy('due_date');

        return view('projects.index', compact('projects'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|max:255',
            'client' => 'nullable|max:255',
            'description' => 'nullable',
            'color' => 'nullable|max:7',
            'status' => 'nullable|in:in_arrivo,in_corso,in_revisione,completato,archiviato',
            'due_date' => 'nullable|date',
            'estimated_hours' => 'nullable|integer|min:0',
        ]);

        $data['user_id'] = auth()->id();
        $data['color'] ??= '#3b82f6';
        $data['status'] ??= 'in_arrivo';

        Project::create($data);

        return redirect()->route('projects.index')->with('success', 'Progetto creato!');
    }

    public function update(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        $data = $request->validate([
            'name' => 'required|max:255',
            'client' => 'nullable|max:255',
            'description' => 'nullable',
            'color' => 'nullable|max:7',
            'status' => 'nullable|in:in_arrivo,in_corso,in_revisione,completato,archiviato',
            'due_date' => 'nullable|date',
            'estimated_hours' => 'nullable|integer|min:0',
        ]);

        $project->update($data);

        return redirect()->route('projects.index')->with('success', 'Progetto aggiornato!');
    }

    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);
        $project->delete();

        return redirect()->route('projects.index')->with('success', 'Progetto eliminato!');
    }
}
