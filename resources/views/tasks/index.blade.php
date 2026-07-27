<x-app-layout>
    <x-slot name="title">Task</x-slot>

    <div class="space-y-6" x-data="taskData()">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div class="flex gap-1 bg-gray-100 dark:bg-gray-800 rounded-xl p-1">
                <button @click="view = 'list'" :class="view === 'list' ? 'bg-white dark:bg-gray-700 shadow-sm' : ''" class="px-4 py-2 rounded-lg text-xs font-semibold transition">Lista</button>
                <button @click="view = 'kanban'" :class="view === 'kanban' ? 'bg-white dark:bg-gray-700 shadow-sm' : ''" class="px-4 py-2 rounded-lg text-xs font-semibold transition">Kanban</button>
                <button @click="view = 'calendar'" :class="view === 'calendar' ? 'bg-white dark:bg-gray-700 shadow-sm' : ''" class="px-4 py-2 rounded-lg text-xs font-semibold transition">Calendario</button>
            </div>
            <button onclick="document.getElementById('taskModal').classList.remove('hidden')" class="btn btn-primary">+ Nuovo task</button>
        </div>

        @if(session('success'))
            <div class="bg-emerald-100 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300 p-4 rounded-xl text-sm font-medium">{{ session('success') }}</div>
        @endif

        <!-- LISTA -->
        <div x-show="view === 'list'" class="space-y-4">
            <div class="flex gap-2 flex-wrap">
                <template x-for="f in ['Tutti','Alta priorità','In scadenza','Completati']" :key="f">
                    <button @click="filter = f" :class="filter === f ? 'bg-blue-600 text-white' : 'bg-white dark:bg-gray-900 text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-800'" class="px-3 py-1.5 text-xs rounded-lg font-semibold transition">@{{f}}</button>
                </template>
            </div>
            <div class="space-y-2">
                @foreach($tasks as $t)
                <div class="card p-4 flex items-center gap-4">
                    <form method="POST" action="{{ route('tasks.update', $t) }}" class="flex items-center gap-4 flex-1">
                        @csrf @method('PATCH')
                        <input type="hidden" name="done" value="{{ $t->status === 'fatto' ? '0' : '1' }}">
                        <button type="submit" class="w-5 h-5 rounded border-2 flex items-center justify-center cursor-pointer transition {{ $t->status === 'fatto' ? 'bg-blue-600 border-blue-600' : 'border-gray-300 dark:border-gray-600 hover:border-blue-400' }}">
                            @if($t->status === 'fatto')<svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>@endif
                        </button>
                    </form>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold {{ $t->status === 'fatto' ? 'line-through text-gray-400' : '' }}">{{ $t->title }}</p>
                        <div class="flex gap-2 mt-1.5 flex-wrap items-center">
                            <span class="badge {{ $t->priority === 'alta' ? 'bg-red-100 text-red-700 dark:bg-red-900/20 dark:text-red-300' : ($t->priority === 'media' ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/20 dark:text-amber-300' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-300') }}">{{ ucfirst($t->priority) }}</span>
                            @if($t->project)<span class="text-[10px] text-gray-400 bg-gray-100 dark:bg-gray-800 px-2 py-0.5 rounded-md">{{ $t->project->name }}</span>@endif
                            @if($t->due_date)<span class="text-[10px] {{ $t->due_date->isPast() && $t->status !== 'fatto' ? 'text-red-500 pulse font-bold' : 'text-gray-400' }}">{{ $t->due_date->format('d/m/Y') }}</span>@endif
                        </div>
                        @if($t->subtasks && count($t->subtasks) > 0)
                        <div class="mt-2 space-y-1">
                            @foreach($t->subtasks as $si => $s)
                            <div class="flex items-center gap-2">
                                <input type="checkbox" {{ $s['done'] ? 'checked' : '' }} class="w-3.5 h-3.5 rounded border-gray-300 text-blue-600 focus:ring-1 focus:ring-blue-500 cursor-pointer">
                                <span class="text-[11px] {{ $s['done'] ? 'line-through text-gray-400' : 'text-gray-600 dark:text-gray-300' }}">{{ $s['title'] ?? ('Subtask '.($si+1)) }}</span>
                            </div>
                            @endforeach
                            <div class="flex items-center gap-2 mt-1.5">
                                <div class="progress-bar flex-1" style="height:4px">
                                    @php
                                        $doneCount = 0;
                                        $totalCount = is_array($t->subtasks) ? count($t->subtasks) : 0;
                                        if ($totalCount > 0) {
                                            foreach ($t->subtasks as $sub) {
                                                if (!empty($sub['done'])) $doneCount++;
                                            }
                                        }
                                        $pct = $totalCount > 0 ? ($doneCount / $totalCount * 100) : 0;
                                    @endphp
                                    <div class="progress-fill" style="width:{{ $pct }}%;background:#10b981"></div>
                                </div>
                                <span class="text-[10px] text-gray-400 font-medium">{{ $doneCount }}/{{ $totalCount }}</span>
                            </div>
                        </div>
                        @endif
                    </div>
                    <!-- Timer -->
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <span class="text-[11px] font-mono tabular-nums font-semibold text-gray-400" x-text="formatTime({{ $t->elapsed ?? 0 }})"></span>
                        <button @click="toggleTimer({{ $t->id }})" class="w-8 h-8 rounded-lg flex items-center justify-center transition" :class="runningTask === {{ $t->id }} ? 'bg-red-100 text-red-600 dark:bg-red-900/20 dark:text-red-300' : 'bg-gray-100 text-gray-500 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400'">
                            <span x-text="runningTask === {{ $t->id }} ? '⏸' : '▶'"></span>
                        </button>
                    </div>
                    <form method="POST" action="{{ route('tasks.destroy', $t) }}" onsubmit="return confirm('Eliminare questo task?')">
                        @csrf @method('DELETE')
                        <button class="text-xs text-gray-300 hover:text-red-500 transition p-1">🗑</button>
                    </form>
                </div>
                @endforeach
            </div>
        </div>

        <!-- KANBAN -->
        <div x-show="view === 'kanban'" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @php $cols = ['Da fare', 'In corso', 'Fatto']; @endphp
            @foreach($cols as $ci => $col)
            <div class="bg-gray-100 dark:bg-gray-800/30 rounded-2xl p-4 min-h-[400px]">
                <h4 class="text-sm font-bold mb-4 flex items-center gap-2">
                    <span>{{ $col }}</span>
                    <span class="text-xs text-gray-400 font-normal">{{ $tasks->filter(function($t) use ($col) { return $t->kanban_col === $col; })->count() }}</span>
                </h4>
                <div class="space-y-2">
                    @foreach($tasks->filter(function($t) use ($col) { return $t->kanban_col === $col; }) as $t)
                    <div class="card p-3">
                        <p class="text-sm font-semibold">{{ $t->title }}</p>
                        <div class="flex gap-1 mt-2">
                            <span class="badge {{ $t->priority === 'alta' ? 'bg-red-100 text-red-700 dark:bg-red-900/20 dark:text-red-300' : ($t->priority === 'media' ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/20 dark:text-amber-300' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-300') }}">{{ ucfirst($t->priority) }}</span>
                        </div>
                        <div class="flex items-center justify-between mt-3 pt-2 border-t border-gray-100 dark:border-gray-800">
                            @if($ci > 0)
                            <form method="POST" action="{{ route('tasks.update', $t) }}">
                                @csrf @method('PATCH')
                                <input type="hidden" name="kanban_col" value="{{ $cols[$ci-1] }}">
                                <button class="text-xs text-gray-400 hover:text-blue-600 px-2 py-1 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/10 transition">←</button>
                            </form>
                            @else <span></span> @endif
                            @if($ci < 2)
                            <form method="POST" action="{{ route('tasks.update', $t) }}">
                                @csrf @method('PATCH')
                                <input type="hidden" name="kanban_col" value="{{ $cols[$ci+1] }}">
                                <button class="text-xs text-gray-400 hover:text-blue-600 px-2 py-1 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/10 transition">→</button>
                            </form>
                            @else <span></span> @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>

        <!-- CALENDARIO -->
        <div x-show="view === 'calendar'" class="card p-5">
            <div class="flex justify-between items-center mb-4">
                <button @click="calMonth--" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition">←</button>
                <span class="font-bold text-sm capitalize" x-text="calMonthName"></span>
                <button @click="calMonth++" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition">→</button>
            </div>
            <div class="grid grid-cols-7 gap-1 text-center text-xs font-semibold text-gray-400 mb-2">
                <template x-for="d in ['Lun','Mar','Mer','Gio','Ven','Sab','Dom']" :key="d"><span x-text="d"></span></template>
            </div>
            <div class="grid grid-cols-7 gap-1">
                <template x-for="(day, idx) in calDays" :key="idx">
                    <div :class="day.today ? 'bg-blue-100 dark:bg-blue-900/20 ring-2 ring-blue-500 font-bold' : day.hasTasks ? 'bg-gray-50 dark:bg-gray-800/50' : ''" class="aspect-square rounded-lg text-xs flex items-center justify-center relative">
                        <span x-text="day.num"></span>
                        <div x-show="day.hasTasks" class="absolute bottom-1 left-1/2 -translate-x-1/2"><span class="w-1.5 h-1.5 rounded-full bg-blue-500 block"></span></div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <div id="taskModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4" onclick="if(event.target===this)this.classList.add('hidden')">
        <div class="bg-white dark:bg-gray-900 rounded-2xl max-w-md w-full p-6 shadow-2xl border border-gray-200 dark:border-gray-800">
            <div class="flex justify-between items-center mb-5">
                <h3 class="text-lg font-bold">Nuovo Task</h3>
                <button onclick="document.getElementById('taskModal').classList.add('hidden')" class="p-1 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg text-gray-400">&times;</button>
            </div>
            <form method="POST" action="{{ route('tasks.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold mb-1.5 text-gray-500">Titolo</label>
                    <input type="text" name="title" required class="input" placeholder="es. Fix header responsive">
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
                    <label class="block text-xs font-semibold mb-1.5 text-gray-500">Progetto</label>
                    <select name="project_id" class="input">
                        <option value="">Nessun progetto</option>
                        @foreach($projects as $p)
                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5 text-gray-500">Scadenza</label>
                    <input type="date" name="due_date" class="input">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5 text-gray-500">Subtask (uno per riga)</label>
                    <textarea name="subtasks" rows="3" class="input" placeholder="Preparare bozza&#10;Inviare al cliente&#10;Applicare revisioni"></textarea>
                </div>
                <div class="flex gap-2 justify-end pt-2">
                    <button type="button" onclick="document.getElementById('taskModal').classList.add('hidden')" class="btn btn-ghost">Annulla</button>
                    <button type="submit" class="btn btn-primary">Salva</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
    function taskData() {
        return {
            view: 'list',
            filter: 'Tutti',
            runningTask: null,
            timerInterval: null,
            calMonth: new Date().getMonth(),
            calYear: new Date().getFullYear(),
            taskElapsed: {!! json_encode($tasks->pluck('elapsed', 'id')) !!},

            get calMonthName() {
                return new Date(this.calYear, this.calMonth).toLocaleDateString('it-IT', {month:'long', year:'numeric'});
            },
            get calDays() {
                var d = [], fd = new Date(this.calYear, this.calMonth, 1).getDay(), dm = new Date(this.calYear, this.calMonth + 1, 0).getDate(), t = new Date(), off = fd === 0 ? 6 : fd - 1;
                for (var i = 0; i < off; i++) d.push({num:'', today:false, hasTasks:false});
                @php
                    $taskDates = [];
                    foreach ($tasks as $tk) {
                        if ($tk->due_date) {
                            $taskDates[] = $tk->due_date->format('j');
                        }
                    }
                @endphp
                var taskDays = {!! json_encode($taskDates) !!};
                for (var j = 1; j <= dm; j++) {
                    var isT = j === t.getDate() && this.calMonth === t.getMonth() && this.calYear === t.getFullYear();
                    d.push({num:j, today:isT, hasTasks: taskDays.indexOf(String(j)) !== -1});
                }
                return d;
            },
            formatTime(sec) {
                if (!sec || sec === 0) return '0m';
                var h = Math.floor(sec / 3600);
                var m = Math.floor((sec % 3600) / 60);
                return h > 0 ? h + 'h ' + m + 'm' : m + 'm';
            },
            toggleTimer(id) {
                if (this.runningTask === id) {
                    this.runningTask = null;
                    if (this.timerInterval) { clearInterval(this.timerInterval); this.timerInterval = null; }
                    // Save elapsed via AJAX
                    var self = this;
                    fetch('/tasks/' + id + '/timer', {
                        method: 'POST',
                        headers: {'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Content-Type': 'application/json'},
                        body: JSON.stringify({elapsed: this.taskElapsed[id] || 0})
                    });
                } else {
                    if (this.timerInterval) { clearInterval(this.timerInterval); this.timerInterval = null; }
                    this.runningTask = id;
                    var self = this;
                    this.timerInterval = setInterval(function() {
                        self.taskElapsed[id] = (self.taskElapsed[id] || 0) + 1;
                    }, 1000);
                }
            }
        }
    }
    </script>
    @endpush
</x-app-layout>