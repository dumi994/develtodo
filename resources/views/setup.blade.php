<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gray-50 dark:bg-gray-950 p-6">
        <div class="max-w-md w-full">
            <div class="text-center mb-8">
                <span class="text-4xl font-extrabold tracking-tight bg-gradient-to-r from-blue-600 to-indigo-500 bg-clip-text text-transparent">DevelTodo</span>
                <p class="text-sm text-gray-400 mt-2">Benvenuto! Configura la tua applicazione</p>
            </div>

            <div class="bg-white dark:bg-gray-900 rounded-2xl p-8 shadow-xl border border-gray-200 dark:border-gray-800">
                @if($seeded)
                    <div class="text-center">
                        <p class="text-emerald-600 dark:text-emerald-400 font-semibold mb-4">✅ Setup già completato</p>
                        <a href="{{ route('dashboard') }}" class="btn btn-primary">Vai alla Dashboard</a>
                    </div>
                @else
                    <form method="POST" action="{{ route('setup.run') }}" class="space-y-6">
                        @csrf
                        <div class="text-center">
                            <span class="text-5xl">🚀</span>
                            <p class="text-sm text-gray-500 mt-4">Il database verrà inizializzato automaticamente. Vuoi anche popolarlo con dati di esempio?</p>
                        </div>

                        <div class="flex items-center justify-center gap-4 p-4 bg-gray-50 dark:bg-gray-800/50 rounded-xl">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="radio" name="seed" value="1" class="w-4 h-4 text-blue-600 focus:ring-blue-500">
                                <span class="text-sm font-medium">Sì, inserisci dati di esempio</span>
                            </label>
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="radio" name="seed" value="0" checked class="w-4 h-4 text-blue-600 focus:ring-blue-500">
                                <span class="text-sm font-medium">No, parti da zero</span>
                            </label>
                        </div>

                        <button type="submit" class="btn btn-primary w-full justify-center text-sm py-3">
                            Avvia DevelTodo
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</x-guest-layout>