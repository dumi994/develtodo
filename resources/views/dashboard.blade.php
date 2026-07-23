<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>

    <div class="space-y-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="card p-5">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-2xl">📁</span>
                    <span class="text-xs text-emerald-600 font-semibold">+2</span>
                </div>
                <p class="text-3xl font-extrabold">8</p>
                <p class="text-xs text-gray-400 mt-1">Progetti attivi</p>
            </div>
            <div class="card p-5">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-2xl">📋</span>
                    <span class="text-xs text-amber-600 font-semibold">5 oggi</span>
                </div>
                <p class="text-3xl font-extrabold">24</p>
                <p class="text-xs text-gray-400 mt-1">Task aperti</p>
            </div>
            <div class="card p-5">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-2xl">🎫</span>
                    <span class="text-xs text-red-600 font-semibold">4 urgenti</span>
                </div>
                <p class="text-3xl font-extrabold">12</p>
                <p class="text-xs text-gray-400 mt-1">Ticket aperti</p>
            </div>
            <div class="card p-5">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-2xl">💰</span>
                    <span class="text-xs text-emerald-600 font-semibold">€12.450</span>
                </div>
                <p class="text-3xl font-extrabold">3</p>
                <p class="text-xs text-gray-400 mt-1">Fatture in ritardo</p>
            </div>
        </div>

        <div class="card p-5">
            <h3 class="font-bold text-sm mb-4">📊 Benvenuto in DevelTodo</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Il tuo gestionale per web developer. Usa la sidebar per navigare tra le sezioni.</p>
        </div>
    </div>
</x-app-layout>