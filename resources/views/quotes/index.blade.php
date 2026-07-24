<x-app-layout>
    <x-slot name="title">Preventivi</x-slot>

    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <p class="text-sm text-gray-400">{{ count($quotes) }} preventivi</p>
            <button onclick="document.getElementById('quoteModal').classList.remove('hidden')" class="btn btn-primary">+ Nuovo preventivo</button>
        </div>

        @if(session('success'))
            <div class="bg-emerald-100 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300 p-4 rounded-xl text-sm font-medium">{{ session('success') }}</div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($quotes as $q)
            <div class="card p-5">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <h4 class="font-bold text-sm">{{ $q->client }}</h4>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $q->description }}</p>
                    </div>
                    <span class="badge {{ $q->status == 'bozza' ? 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300' : ($q->status == 'inviato' ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/20 dark:text-amber-300' : ($q->status == 'accettato' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-300' : ($q->status == 'rifiutato' ? 'bg-red-100 text-red-700 dark:bg-red-900/20 dark:text-red-300' : 'bg-blue-100 text-blue-700 dark:bg-blue-900/20 dark:text-blue-300'))) }}">{{ ucfirst($q->status) }}</span>
                </div>
                <div class="mt-3 flex items-center justify-between">
                    <span class="text-2xl font-extrabold">€ {{ number_format($q->amount, 2, ',', '.') }}</span>
                    <span class="text-xs text-gray-400">Scade: {{ $q->expiry_date ? $q->expiry_date->format('d/m/Y') : '-' }}</span>
                </div>
                @if($q->file_path)
                <div class="mt-2 flex items-center gap-1.5 text-xs text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    <a href="{{ asset('storage/'.$q->file_path) }}" target="_blank" class="hover:text-blue-600">preventivo.pdf</a>
                </div>
                @endif
                @if($q->status == 'accettato')
                <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-800">
                    <a href="{{ route('quotes.createInvoice', $q) }}" class="btn btn-success w-full justify-center">Crea fattura da preventivo</a>
                </div>
                @endif
                <div class="mt-3 flex items-center gap-2 text-xs text-gray-400">
                    <span>Stato:</span>
                    <form method="POST" action="{{ route('quotes.update', $q) }}" class="flex items-center gap-2">
                        @csrf @method('PATCH')
                        <select name="status" onchange="this.form.submit()" class="text-xs border border-gray-200 dark:border-gray-700 rounded-lg px-2 py-1 bg-transparent">
                            <option value="bozza" {{ $q->status == 'bozza' ? 'selected' : '' }}>Bozza</option>
                            <option value="inviato" {{ $q->status == 'inviato' ? 'selected' : '' }}>Inviato</option>
                            <option value="accettato" {{ $q->status == 'accettato' ? 'selected' : '' }}>Accettato</option>
                            <option value="rifiutato" {{ $q->status == 'rifiutato' ? 'selected' : '' }}>Rifiutato</option>
                            <option value="fatturato" {{ $q->status == 'fatturato' ? 'selected' : '' }}>Fatturato</option>
                        </select>
                    </form>
                    <form method="POST" action="{{ route('quotes.destroy', $q) }}" class="ml-auto" onsubmit="return confirm('Eliminare?')">
                        @csrf @method('DELETE')
                        <button class="text-xs text-gray-400 hover:text-red-500 transition">🗑</button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Create Modal -->
    <div id="quoteModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4" onclick="if(event.target===this)this.classList.add('hidden')">
        <div class="bg-white dark:bg-gray-900 rounded-2xl max-w-md w-full p-6 shadow-2xl border border-gray-200 dark:border-gray-800">
            <div class="flex justify-between items-center mb-5">
                <h3 class="text-lg font-bold">Nuovo Preventivo</h3>
                <button onclick="document.getElementById('quoteModal').classList.add('hidden')" class="p-1 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg text-gray-400">&times;</button>
            </div>
            <form method="POST" action="{{ route('quotes.store') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold mb-1.5 text-gray-500">Cliente</label>
                    <input type="text" name="client" required class="input" placeholder="Nome cliente">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5 text-gray-500">Importo (€)</label>
                    <input type="number" name="amount" step="0.01" required class="input" placeholder="es. 2500">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5 text-gray-500">Descrizione</label>
                    <input type="text" name="description" class="input" placeholder="es. Sito vetrina + blog">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5 text-gray-500">Scadenza validità</label>
                    <input type="date" name="expiry_date" class="input">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5 text-gray-500">Documento PDF</label>
                    <input type="file" name="file" accept=".pdf" class="input">
                </div>
                <div class="flex gap-2 justify-end pt-2">
                    <button type="button" onclick="document.getElementById('quoteModal').classList.add('hidden')" class="btn btn-ghost">Annulla</button>
                    <button type="submit" class="btn btn-primary">Salva</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>