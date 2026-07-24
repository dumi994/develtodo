<x-app-layout>
    <x-slot name="title">Progetti</x-slot>

    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <p class="text-sm text-gray-400">{{ count($projects) }} progetti</p>
            <button onclick="document.getElementById('createModal').classList.remove('hidden')" class="btn btn-primary">+ Nuovo progetto</button>
        </div>

        @if(session('success'))
            <div class="bg-emerald-100 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300 p-4 rounded-xl text-sm font-medium">{{ session('success') }}</div>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($projects as $p)
            <div class="card p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full" style="background: {{ $p->color }}"></span>
                        <span class="badge {{ $p->status == 'in_arrivo' ? 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300' : ($p->status == 'in_corso' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/20 dark:text-blue-300' : ($p->status == 'in_revisione' ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/20 dark:text-amber-300' : ($p->status == 'completato' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-300' : 'bg-gray-200 text-gray-500 dark:bg-gray-700 dark:text-gray-400'))) }}">{{ $p->status == 'in_arrivo' ? 'In arrivo' : ($p->status == 'in_corso' ? 'In corso' : ($p->status == 'in_revisione' ? 'In revisione' : ($p->status == 'completato' ? 'Completato' : 'Archiviato'))) }}</span>
                    </div>
                    <span class="text-xs text-gray-400">{{ $p->due_date ? $p->due_date->format('d/m/Y') : '' }}</span>
                </div>
                <h4 class="font-bold text-base">{{ $p->name }}</h4>
                <p class="text-xs text-gray-400 mt-1">{{ $p->client }}</p>
                <div class="mt-4 flex justify-between text-xs text-gray-400 mb-1.5">
                    <span>{{ $p->tasks_count ?? 0 }} task</span>
                    <span class="font-semibold">{{ $p->progress ?? 0 }}%</span>
                </div>
                <div class="progress-bar"><div class="progress-fill" style="width:{{ $p->progress ?? 0 }}%;background:{{ $p->color }}"></div></div>

                <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between">
                    <form method="POST" action="{{ route('projects.update', $p) }}" class="flex items-center gap-2">
                        @csrf @method('PATCH')
                        <select name="status" onchange="this.form.submit()" class="text-xs border border-gray-200 dark:border-gray-700 rounded-lg px-2 py-1 bg-transparent">
                            <option value="in_arrivo" {{ $p->status == 'in_arrivo' ? 'selected' : '' }}>In arrivo</option>
                            <option value="in_corso" {{ $p->status == 'in_corso' ? 'selected' : '' }}>In corso</option>
                            <option value="in_revisione" {{ $p->status == 'in_revisione' ? 'selected' : '' }}>In revisione</option>
                            <option value="completato" {{ $p->status == 'completato' ? 'selected' : '' }}>Completato</option>
                            <option value="archiviato" {{ $p->status == 'archiviato' ? 'selected' : '' }}>Archiviato</option>
                        </select>
                    </form>
                    <form method="POST" action="{{ route('projects.destroy', $p) }}" onsubmit="return confirm('Eliminare questo progetto?')">
                        @csrf @method('DELETE')
                        <button class="text-xs text-gray-400 hover:text-red-500 transition">🗑</button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Create Modal -->
    <div id="createModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4" onclick="if(event.target===this)this.classList.add('hidden')">
        <div class="bg-white dark:bg-gray-900 rounded-2xl max-w-md w-full p-6 shadow-2xl border border-gray-200 dark:border-gray-800">
            <div class="flex justify-between items-center mb-5">
                <h3 class="text-lg font-bold">Nuovo Progetto</h3>
                <button onclick="document.getElementById('createModal').classList.add('hidden')" class="p-1 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg text-gray-400">&times;</button>
            </div>
            <form method="POST" action="{{ route('projects.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold mb-1.5 text-gray-500">Nome progetto</label>
                    <input type="text" name="name" required class="input" placeholder="es. Sito Web Agency">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5 text-gray-500">Cliente</label>
                    <input type="text" name="client" class="input" placeholder="es. Creative Lab">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5 text-gray-500">Data scadenza</label>
                    <input type="date" name="due_date" class="input">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5 text-gray-500">Ore stimate</label>
                    <input type="number" name="estimated_hours" class="input" placeholder="es. 40">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5 text-gray-500">Colore</label>
                    <select name="color" class="input">
                        <option value="#3b82f6">Blu</option>
                        <option value="#f59e0b">Giallo</option>
                        <option value="#10b981">Verde</option>
                        <option value="#8b5cf6">Viola</option>
                        <option value="#ec4899">Rosa</option>
                        <option value="#06b6d4">Azzurro</option>
                    </select>
                </div>
                <div class="flex gap-2 justify-end pt-2">
                    <button type="button" onclick="document.getElementById('createModal').classList.add('hidden')" class="btn btn-ghost">Annulla</button>
                    <button type="submit" class="btn btn-primary">Salva</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>