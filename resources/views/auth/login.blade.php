<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" class="text-gray-300" />
            <x-text-input id="email" class="block mt-1 w-full bg-gray-800/50 border-gray-700 text-white" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="admin@develtodo.local" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" class="text-gray-300" />

            <x-text-input id="password" class="block mt-1 w-full bg-gray-800/50 border-gray-700 text-white"
                            type="password"
                            name="password"
                            required autocomplete="current-password"
                            placeholder="••••••••" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-600 bg-gray-800 text-blue-600 shadow-sm focus:ring-blue-500" name="remember">
                <span class="ms-2 text-sm text-gray-400">{{ __('Ricordami') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-6">
            <x-primary-button class="ms-3 w-full justify-center py-2.5">
                {{ __('Accedi') }}
            </x-primary-button>
        </div>
    </form>

    <div class="mt-6 p-4 bg-blue-500/10 rounded-xl border border-blue-500/20">
        <p class="text-xs font-semibold text-blue-300 mb-2">🔐 Credenziali predefinite</p>
        <div class="text-xs text-gray-400 space-y-1 font-mono">
            <p>Email: <span class="text-white font-bold">admin@develtodo.local</span></p>
            <p>Password: <span class="text-white font-bold">admin123</span></p>
        </div>
        <p class="text-[10px] text-gray-500 mt-2">Puoi cambiarle dal profilo dopo l'accesso.</p>
    </div>
</x-guest-layout>
