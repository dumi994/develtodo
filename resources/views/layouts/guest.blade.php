<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'DevelTodo') }} — Accedi</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 dark:text-gray-100 antialiased min-h-screen bg-gradient-to-br from-gray-950 via-gray-900 to-blue-950">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            <div class="mb-8 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 shadow-lg shadow-blue-500/30 mb-4">
                    <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
                <h1 class="text-3xl font-extrabold tracking-tight bg-gradient-to-r from-blue-400 to-indigo-400 bg-clip-text text-transparent">DevelTodo</h1>
                <p class="text-xs text-gray-500 mt-1">Gestione progetti per sviluppatori</p>
            </div>

            <div class="w-full sm:max-w-md mt-2 px-8 py-8 bg-white/5 dark:bg-gray-900/60 backdrop-blur-xl border border-white/10 shadow-2xl rounded-2xl">
                {{ $slot }}
            </div>

            <p class="mt-8 text-[11px] text-gray-600">DevelTodo v1.0.6 · App desktop locale</p>
        </div>
    </body>
</html>
