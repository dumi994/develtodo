<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use Illuminate\Http\Request;

class QuoteController extends Controller
{
    public function index()
    {
        $quotes = Quote::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();
        return view('quotes.index', compact('quotes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'client' => 'required|max:255',
            'description' => 'nullable|max:255',
            'amount' => 'required|numeric|min:0',
            'expiry_date' => 'nullable|date',
            'file' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        $data['user_id'] = auth()->id();
        $data['status'] = 'bozza';

        if ($request->hasFile('file')) {
            $data['file_path'] = $request->file('file')->store('quotes', 'public');
        }

        Quote::create($data);

        return redirect()->route('quotes.index')->with('success', 'Preventivo creato!');
    }

    public function update(Request $request, Quote $quote)
    {
        $this->authorize('update', $quote);

        $data = $request->validate([
            'client' => 'required|max:255',
            'description' => 'nullable|max:255',
            'amount' => 'required|numeric|min:0',
            'status' => 'nullable|in:bozza,inviato,accettato,rifiutato,fatturato',
            'expiry_date' => 'nullable|date',
        ]);

        $quote->update($data);

        return redirect()->route('quotes.index')->with('success', 'Preventivo aggiornato!');
    }

    public function destroy(Quote $quote)
    {
        $this->authorize('delete', $quote);
        $quote->delete();
        return redirect()->route('quotes.index')->with('success', 'Preventivo eliminato!');
    }
}
