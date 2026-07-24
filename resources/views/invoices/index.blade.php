<x-app-layout>
    <x-slot name="title">Fatture</x-slot>

    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <p class="text-sm text-gray-400">{{ count($invoices) }} fatture</p>
            <button onclick="document.getElementById('invoiceModal').classList.remove('hidden')" class="btn btn-primary">+ Nuova fattura</button>
        </div>

        @if(session('success'))
            <div class="bg-emerald-100 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300 p-4 rounded-xl text-sm font-medium">{{ session('success') }}</div>
        @endif

        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-400 text-xs border-b border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/30">
                            <th class="p-4 font-semibold">#</th>
                            <th class="p-4 font-semibold">Cliente</th>
                            <th class="p-4 font-semibold">Importo</th>
                            <th class="p-4 font-semibold">Scadenza</th>
                            <th class="p-4 font-semibold">Stato</th>
                            <th class="p-4 font-semibold"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoices as $inv)
                        @php $overdue = $inv->status !== 'pagata' && $inv->due_date && $inv->due_date->isPast(); @endphp
                        <tr class="border-b border-gray-100 dark:border-gray-800/50 hover:bg-gray-50 dark:hover:bg-gray-800/20 transition {{ $overdue ? 'bg-red-50 dark:bg-red-900/5' : '' }}">
                            <td class="p-4 text-gray-400 font-mono">{{ $inv->number }}</td>
                            <td class="p-4 font-semibold">{{ $inv->client }}</td>
                            <td class="p-4">€ {{ number_format($inv->amount, 2, ',', '.') }}</td>
                            <td class="p-4 {{ $overdue ? 'text-red-500 pulse font-bold' : '' }}">{{ $inv->due_date ? $inv->due_date->format('d/m/Y') : '' }}</td>
                            <td class="p-4">
                                <span class="badge {{ $inv->status == 'da_fare' ? 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300' : ($inv->status == 'inviata' ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/20 dark:text-amber-300' : ($inv->status == 'pagata' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-300' : 'bg-red-100 text-red-700 dark:bg-red-900/20 dark:text-red-300')) }}">{{ $inv->status == 'da_fare' ? 'Da fare' : ($inv->status == 'inviata' ? 'Inviata' : ($inv->status == 'pagata' ? 'Pagata' : 'Sollecito')) }}</span>
                            </td>
                            <td class="p-4">
                                <form method="POST" action="{{ route('invoices.update', $inv) }}" class="flex items-center gap-2">
                                    @csrf @method('PATCH')
                                    <select name="status" onchange="this.form.submit()" class="text-xs border border-gray-200 dark:border-gray-700 rounded-lg px-2 py-1 bg-transparent">
                                        <option value="da_fare" {{ $inv->status == 'da_fare' ? 'selected' : '' }}>Da fare</option>
                                        <option value="inviata" {{ $inv->status == 'inviata' ? 'selected' : '' }}>Inviata</option>
                                        <option value="pagata" {{ $inv->status == 'pagata' ? 'selected' : '' }}>Pagata</option>
                                        <option value="sollecito" {{ $inv->status == 'sollecito' ? 'selected' : '' }}>Sollecito</option>
                                    </select>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <div id="invoiceModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4" onclick="if(event.target===this)this.classList.add('hidden')">
        <div class="bg-white dark:bg-gray-900 rounded-2xl max-w-md w-full p-6 shadow-2xl border border-gray-200 dark:border-gray-800">
            <div class="flex justify-between items-center mb-5">
                <h3 class="text-lg font-bold">Nuova Fattura</h3>
                <button onclick="document.getElementById('invoiceModal').classList.add('hidden')" class="p-1 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg text-gray-400">&times;</button>
            </div>
            <form method="POST" action="{{ route('invoices.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold mb-1.5 text-gray-500">Cliente</label>
                    <input type="text" name="client" required class="input" placeholder="Nome cliente">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5 text-gray-500">Importo (€)</label>
                    <input type="number" name="amount" step="0.01" required class="input" placeholder="es. 3200">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5 text-gray-500">Scadenza</label>
                    <input type="date" name="due_date" class="input">
                </div>
                <div class="flex gap-2 justify-end pt-2">
                    <button type="button" onclick="document.getElementById('invoiceModal').classList.add('hidden')" class="btn btn-ghost">Annulla</button>
                    <button type="submit" class="btn btn-primary">Salva</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>