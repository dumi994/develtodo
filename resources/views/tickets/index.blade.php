<x-app-layout>
    <x-slot name="title">Ticket</x-slot>

    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <p class="text-sm text-gray-400">{{ count($tickets) }} ticket</p>
            <button onclick="document.getElementById('ticketModal').classList.remove('hidden')" class="btn btn-primary">+ Nuovo ticket</button>
        </div>

        @if(session('success'))
            <div class="bg-emerald-100 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300 p-4 rounded-xl text-sm font-medium">{{ session('success') }}</div>
        @endif

        <div class="space-y-2">
            @foreach($tickets as $t)
            <div class="card">
                <div class="p-4 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800/30 rounded-2xl transition flex items-center justify-between" onclick="this.nextElementSibling.classList.toggle('hidden')">
                    <div class="flex items-center gap-3 flex-1 min-w-0">
                        <span class="badge flex-shrink-0 {{ $t->priority == 'alta' ? 'bg-red-100 text-red-700 dark:bg-red-900/20 dark:text-red-300' : ($t->priority == 'media' ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/20 dark:text-amber-300' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-300') }}">{{ ucfirst($t->priority) }}</span>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold truncate">{{ $t->subject }}</p>
                            <p class="text-xs text-gray-400 truncate">{{ $t->client }} · {{ $t->category == 'bug' ? '🐛 Bug' : ($t->category == 'feature' ? '✨ Feature' : '❓ Supporto') }} · {{ $t->last_update ? $t->last_update->format('d/m/Y') : '' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 flex-shrink-0">
                        <span class="badge {{ $t->status == 'aperto' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/20 dark:text-blue-300' : ($t->status == 'in_lavorazione' ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/20 dark:text-amber-300' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-300') }}">{{ $t->status == 'aperto' ? 'Aperto' : ($t->status == 'in_lavorazione' ? 'In lavorazione' : 'Chiuso') }}</span>
                        <svg class="w-4 h-4 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                </div>
                <div class="hidden border-t border-gray-100 dark:border-gray-800">
                    <div class="p-4 space-y-3">
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div><span class="text-xs text-gray-400 block">Cliente</span><span class="font-semibold">{{ $t->client }}</span></div>
                            <div><span class="text-xs text-gray-400 block">Categoria</span><span class="font-semibold">{{ $t->category == 'bug' ? '🐛 Bug' : ($t->category == 'feature' ? '✨ Feature' : '❓ Supporto') }}</span></div>
                            <div><span class="text-xs text-gray-400 block">Priorità</span><span class="font-semibold">{{ ucfirst($t->priority) }}</span></div>
                            <div><span class="text-xs text-gray-400 block">Ultimo agg.</span><span class="font-semibold">{{ $t->last_update ? $t->last_update->format('d/m/Y') : '' }}</span></div>
                        </div>
                        @if($t->notes)
                        <div>
                            <span class="text-xs text-gray-400 block mb-1">Note</span>
                            <p class="text-sm bg-gray-50 dark:bg-gray-800/50 p-3 rounded-xl">{{ $t->notes }}</p>
                        </div>
                        @endif
                    </div>
                    <div class="px-4 pb-4 flex items-center gap-2 text-xs text-gray-400">
                        <span>Stato:</span>
                        <form method="POST" action="{{ route('tickets.update', $t) }}" class="flex items-center gap-2">
                            @csrf @method('PATCH')
                            <select name="status" onchange="this.form.submit()" class="text-xs border border-gray-200 dark:border-gray-700 rounded-lg px-2 py-1 bg-transparent">
                                <option value="aperto" {{ $t->status == 'aperto' ? 'selected' : '' }}>Aperto</option>
                                <option value="in_lavorazione" {{ $t->status == 'in_lavorazione' ? 'selected' : '' }}>In lavorazione</option>
                                <option value="chiuso" {{ $t->status == 'chiuso' ? 'selected' : '' }}>Chiuso</option>
                            </select>
                        </form>
                        <form method="POST" action="{{ route('tickets.destroy', $t) }}" class="ml-auto" onsubmit="return confirm('Eliminare?')">
                            @csrf @method('DELETE')
                            <button class="text-xs text-gray-400 hover:text-red-500 transition">🗑</button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Create Modal -->
    <div id="ticketModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4" onclick="if(event.target===this)this.classList.add('hidden')">
        <div class="bg-white dark:bg-gray-900 rounded-2xl max-w-md w-full p-6 shadow-2xl border border-gray-200 dark:border-gray-800">
            <div class="flex justify-between items-center mb-5">
                <h3 class="text-lg font-bold">Nuovo Ticket</h3>
                <button onclick="document.getElementById('ticketModal').classList.add('hidden')" class="p-1 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg text-gray-400">&times;</button>
            </div>
            <form method="POST" action="{{ route('tickets.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold mb-1.5 text-gray-500">Cliente</label>
                    <input type="text" name="client" required class="input" placeholder="Nome cliente">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5 text-gray-500">Oggetto</label>
                    <input type="text" name="subject" required class="input" placeholder="es. Errore pagina contatti">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5 text-gray-500">Priorità</label>
                    <select name="priority" class="input">
                        <option value="media">Media</option>
                        <option value="alta">Alta</option>
                        <option value="bassa">Bassa</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5 text-gray-500">Categoria</label>
                    <select name="category" class="input">
                        <option value="bug">🐛 Bug</option>
                        <option value="feature">✨ Feature</option>
                        <option value="supporto">❓ Supporto</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5 text-gray-500">Note</label>
                    <textarea name="notes" rows="3" class="input" placeholder="Descrivi il problema..."></textarea>
                </div>
                <div class="flex gap-2 justify-end pt-2">
                    <button type="button" onclick="document.getElementById('ticketModal').classList.add('hidden')" class="btn btn-ghost">Annulla</button>
                    <button type="submit" class="btn btn-primary">Salva</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>