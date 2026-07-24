<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = Ticket::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();
        return view('tickets.index', compact('tickets'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'client' => 'required|max:255',
            'subject' => 'required|max:255',
            'priority' => 'nullable|in:alta,media,bassa',
            'category' => 'nullable|in:bug,feature,supporto',
            'notes' => 'nullable',
        ]);

        $data['user_id'] = auth()->id();
        $data['status'] = 'aperto';
        $data['last_update'] = now();

        Ticket::create($data);

        return redirect()->route('tickets.index')->with('success', 'Ticket creato!');
    }

    public function update(Request $request, Ticket $ticket)
    {
        $this->authorize('update', $ticket);

        $data = $request->validate([
            'client' => 'required|max:255',
            'subject' => 'required|max:255',
            'priority' => 'nullable|in:alta,media,bassa',
            'category' => 'nullable|in:bug,feature,supporto',
            'status' => 'nullable|in:aperto,in_lavorazione,chiuso',
            'notes' => 'nullable',
        ]);

        $data['last_update'] = now();

        $ticket->update($data);

        return redirect()->route('tickets.index')->with('success', 'Ticket aggiornato!');
    }

    public function destroy(Ticket $ticket)
    {
        $this->authorize('delete', $ticket);
        $ticket->delete();
        return redirect()->route('tickets.index')->with('success', 'Ticket eliminato!');
    }
}
