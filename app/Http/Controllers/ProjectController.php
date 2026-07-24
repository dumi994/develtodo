<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::where('user_id', auth()->id())
            ->orderByRaw("FIELD(status, 'in_corso', 'in_revisione', 'in_arrivo', 'completato', 'archiviato')")
            ->orderBy('due_date')
            ->get();

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
