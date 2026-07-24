<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $q = $request->input('q');

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $results = [];

        $projects = \App\Models\Project::where('user_id', auth()->id())
            ->where('name', 'like', "%{$q}%")
            ->orWhere('client', 'like', "%{$q}%")
            ->get();

        foreach ($projects as $p) {
            $results[] = [
                'type' => 'project',
                'icon' => '📁',
                'title' => $p->name,
                'sub' => $p->client ?? '',
                'url' => route('projects.index'),
            ];
        }

        $tasks = \App\Models\Task::where('user_id', auth()->id())
            ->where('title', 'like', "%{$q}%")
            ->get();

        foreach ($tasks as $t) {
            $results[] = [
                'type' => 'task',
                'icon' => '📋',
                'title' => $t->title,
                'sub' => $t->project ? $t->project->name : 'Task libero',
                'url' => route('tasks.index'),
            ];
        }

        $tickets = \App\Models\Ticket::where('user_id', auth()->id())
            ->where('subject', 'like', "%{$q}%")
            ->orWhere('client', 'like', "%{$q}%")
            ->get();

        foreach ($tickets as $t) {
            $results[] = [
                'type' => 'ticket',
                'icon' => '🎫',
                'title' => $t->subject,
                'sub' => $t->client,
                'url' => route('tickets.index'),
            ];
        }

        $quotes = \App\Models\Quote::where('user_id', auth()->id())
            ->where('client', 'like', "%{$q}%")
            ->get();

        foreach ($quotes as $qM) {
            $results[] = [
                'type' => 'quote',
                'icon' => '📄',
                'title' => $qM->client,
                'sub' => '€ ' . number_format($qM->amount, 2, ',', '.'),
                'url' => route('quotes.index'),
            ];
        }

        $invoices = \App\Models\Invoice::where('user_id', auth()->id())
            ->where('client', 'like', "%{$q}%")
            ->orWhere('number', 'like', "%{$q}%")
            ->get();

        foreach ($invoices as $i) {
            $results[] = [
                'type' => 'invoice',
                'icon' => '💰',
                'title' => $i->number,
                'sub' => $i->client,
                'url' => route('invoices.index'),
            ];
        }

        return response()->json($results);
    }
}
