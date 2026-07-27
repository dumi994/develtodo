<x-app-layout>
    <x-slot name="title">Notifiche</x-slot>

    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">Notifiche</h1>
                <p class="text-sm text-gray-500 mt-1">{{ $unreadCount }} non lette su {{ $notifications->count() }}</p>
            </div>
            <form method="POST" action="{{ route('notifications.markAllRead') }}">
                @csrf
                <button type="submit" class="btn btn-ghost text-xs">Segna tutte come lette</button>
            </form>
        </div>

        @if(session('success'))
            <div class="bg-emerald-100 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300 p-4 rounded-xl text-sm font-medium">{{ session('success') }}</div>
        @endif

        <div class="space-y-2">
            @forelse($notifications as $n)
                <div class="card p-4 flex items-start gap-4 {{ $n->read ? 'opacity-60' : 'border-l-4 border-l-blue-500' }}">
                    <span class="text-2xl">{{ $n->icon ?? '🔔' }}</span>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold {{ $n->read ? 'text-gray-500' : '' }}">{{ $n->title }}</p>
                        <p class="text-[11px] text-gray-400 mt-1">{{ $n->created_at->diffForHumans() }}</p>
                    </div>
                    @if(!$n->read)
                        <form method="POST" action="{{ route('notifications.read', $n->id) }}">
                            @csrf
                            <button type="submit" class="text-xs text-blue-600 hover:text-blue-800 font-medium">Segna letta</button>
                        </form>
                    @endif
                </div>
            @empty
                <div class="card p-12 text-center">
                    <span class="text-5xl">📭</span>
                    <p class="text-sm text-gray-400 mt-4">Nessuna notifica</p>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
