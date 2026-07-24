<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="appLayout()" :class="darkMode ? 'dark' : ''">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'DevelTodo')</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-gray-50 dark:bg-gray-950 text-gray-900 dark:text-gray-100 font-sans antialiased">
<div class="flex h-screen overflow-hidden">

    <!-- Sidebar -->
    <aside x-bind:style="sidebarOpen ? 'width:240px' : 'width:60px'" class="flex-shrink-0 bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800 flex flex-col transition-all duration-300 z-20">
        <div class="h-16 flex items-center px-5 border-b border-gray-100 dark:border-gray-800">
            <span x-show="sidebarOpen" class="text-xl font-extrabold tracking-tight bg-gradient-to-r from-blue-600 to-indigo-500 bg-clip-text text-transparent">DevelTodo</span>
            <span x-show="!sidebarOpen" class="text-xl font-extrabold text-blue-600">DT</span>
        </div>
        <nav class="flex-1 py-4 px-3 space-y-1 overflow-y-auto scroll-thin">
            <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <span class="text-lg">📊</span>
                <span x-show="sidebarOpen">Dashboard</span>
            </a>
            <a href="{{ route('projects.index') }}" class="nav-item {{ request()->routeIs('projects.*') ? 'active' : '' }}">
                <span class="text-lg">📁</span>
                <span x-show="sidebarOpen">Progetti</span>
            </a>
            <a href="{{ route('tasks.index') }}" class="nav-item {{ request()->routeIs('tasks.*') ? 'active' : '' }}">
                <span class="text-lg">📋</span>
                <span x-show="sidebarOpen">Task</span>
            </a>
            <a href="{{ route('tickets.index') }}" class="nav-item {{ request()->routeIs('tickets.*') ? 'active' : '' }}">
                <span class="text-lg">🎫</span>
                <span x-show="sidebarOpen">Ticket</span>
            </a>
            <a href="{{ route('quotes.index') }}" class="nav-item {{ request()->routeIs('quotes.*') ? 'active' : '' }}">
                <span class="text-lg">📄</span>
                <span x-show="sidebarOpen">Preventivi</span>
            </a>
            <a href="{{ route('invoices.index') }}" class="nav-item {{ request()->routeIs('invoices.*') ? 'active' : '' }}">
                <span class="text-lg">💰</span>
                <span x-show="sidebarOpen">Fatture</span>
            </a>
        </nav>
        <div class="p-3 border-t border-gray-100 dark:border-gray-800">
            <div class="flex items-center gap-3 px-2">
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-xs font-bold">{{ substr(Auth::user()->name ?? 'M', 0, 1) }}</div>
                <div x-show="sidebarOpen">
                    <p class="text-sm font-semibold">{{ Auth::user()->name ?? 'Marco' }}</p>
                    <p class="text-[10px] text-gray-400">Developer</p>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main -->
    <div class="flex-1 flex flex-col min-w-0">
        <header class="h-16 flex-shrink-0 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 flex items-center justify-between px-6">
            <div>
                <h1 class="text-lg font-bold">@yield('title', 'Dashboard')</h1>
                <p class="text-xs text-gray-400" x-text="currentDate"></p>
            </div>
            <div class="flex items-center gap-3">
                <!-- Notifiche -->
                <div class="relative" x-data="{ notifOpen: false, notifs: [], unread: 0 }" x-init="fetch('/notifications').then(r=>r.json()).then(d=>{ notifs=d.notifications; unread=d.unreadCount; })" @click.outside="notifOpen = false">
                    <button @click="notifOpen = !notifOpen; if(notifOpen) fetch('/notifications').then(r=>r.json()).then(d=>{ notifs=d.notifications; unread=d.unreadCount; })" class="relative p-2 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        <span x-show="unread > 0" class="absolute -top-0.5 -right-0.5 bg-red-500 text-white text-[10px] font-bold rounded-full w-4.5 h-4.5 flex items-center justify-center" x-text="unread"></span>
                    </button>
                    <div x-show="notifOpen" x-cloak class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 z-50 max-h-80 overflow-y-auto fade-in">
                        <div class="p-3 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                            <span class="font-semibold text-sm">Notifiche</span>
                            <button @click="fetch('/notifications/mark-all-read', {method:'POST', headers:{'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content}}).then(()=>{ unread=0; notifs.forEach(n=>n.read=true); })" class="text-xs text-blue-600 hover:underline">Tutte lette</button>
                        </div>
                        <template x-for="n in notifs" :key="n.id">
                            <div @click="if(!n.read){ fetch('/notifications/'+n.id+'/read', {method:'POST', headers:{'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content}}).then(()=>{ n.read=true; unread--; }); }" :class="n.read ? 'opacity-50' : 'bg-blue-50 dark:bg-blue-900/10'" class="p-3 border-b border-gray-100 dark:border-gray-700/30 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                                <div class="flex gap-3">
                                    <span x-text="n.icon" class="text-base"></span>
                                    <div><p class="text-sm font-medium" x-text="n.title"></p><p class="text-xs text-gray-400" x-text="n.created_at ? new Date(n.created_at).toLocaleDateString('it-IT') : ''"></p></div>
                                </div>
                            </div>
                        </template>
                        <div x-show="notifs.length === 0" class="p-6 text-center text-sm text-gray-400">Nessuna notifica</div>
                    </div>
                </div>
                <button @click="darkMode = !darkMode; localStorage.setItem('theme', darkMode ? 'dark' : 'light')" class="p-2 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                    <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                    <svg x-show="darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </button>
                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                    <button @click="open = !open" class="flex items-center gap-2 p-1.5 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                        <div class="w-7 h-7 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-xs font-bold">{{ substr(Auth::user()->name ?? 'M', 0, 1) }}</div>
                        <span class="text-sm font-medium hidden sm:block">{{ Auth::user()->name ?? 'Marco' }}</span>
                    </button>
                    <div x-show="open" x-cloak class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 z-50 fade-in">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-3 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 rounded-xl transition">Logout</button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-6 scroll-thin">
            {{ $slot }}
        </main>
    </div>
</div>

@stack('scripts')
<script>
function appLayout() {
    return {
        sidebarOpen: true,
        darkMode: localStorage.getItem('theme') === 'dark',
        get currentDate() {
            return new Date().toLocaleDateString('it-IT', { weekday:'long', year:'numeric', month:'long', day:'numeric' });
        }
    }
}
</script>
</body>
</html>