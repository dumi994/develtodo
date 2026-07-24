<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();
        return view('invoices.index', compact('invoices'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'client' => 'required|max:255',
            'amount' => 'required|numeric|min:0',
            'due_date' => 'nullable|date',
            'status' => 'nullable|in:da_fare,inviata,pagata,sollecito',
        ]);

        $data['user_id'] = auth()->id();
        $data['status'] ??= 'da_fare';
        $data['number'] = 'INV-' . now()->format('Ymd') . '-' . str_pad(Invoice::whereYear('created_at', now()->year)->count() + 1, 3, '0', STR_PAD_LEFT);

        Invoice::create($data);

        return redirect()->route('invoices.index')->with('success', 'Fattura creata!');
    }

    public function update(Request $request, Invoice $invoice)
    {
        $this->authorize('update', $invoice);

        $data = $request->validate([
            'client' => 'required|max:255',
            'amount' => 'required|numeric|min:0',
            'status' => 'nullable|in:da_fare,inviata,pagata,sollecito',
            'due_date' => 'nullable|date',
        ]);

        if ($data['status'] === 'pagata' && !$invoice->paid_at) {
            $data['paid_at'] = now();
        }
        if ($data['status'] === 'inviata' && !$invoice->sent_at) {
            $data['sent_at'] = now();
        }

        $invoice->update($data);

        return redirect()->route('invoices.index')->with('success', 'Fattura aggiornata!');
    }

    public function destroy(Invoice $invoice)
    {
        $this->authorize('delete', $invoice);
        $invoice->delete();
        return redirect()->route('invoices.index')->with('success', 'Fattura eliminata!');
    }
}
