<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <!-- Demo Accounts Helper -->
    <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-lg text-sm text-emerald-900 shadow-sm">
        <p class="font-semibold text-emerald-800 mb-2 flex items-center gap-1.5">
            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0 118 0z"></path></svg>
            Akun Demo / Quick Login:
        </p>
        <div class="grid grid-cols-2 gap-2">
            <button type="button" onclick="fillCredentials('admin@tuksay.test', 'password')" class="text-left p-2 bg-white border border-emerald-300 rounded hover:bg-emerald-100/80 transition cursor-pointer">
                <span class="block text-xs font-bold text-emerald-700 uppercase tracking-wider">Admin</span>
                <span class="block font-medium text-slate-800 text-xs truncate">admin@tuksay.test</span>
                <span class="block text-[11px] text-slate-500">Pass: <code class="bg-slate-100 px-1 rounded">password</code></span>
            </button>
            <button type="button" onclick="fillCredentials('staff@tuksay.test', 'password')" class="text-left p-2 bg-white border border-emerald-300 rounded hover:bg-emerald-100/80 transition cursor-pointer">
                <span class="block text-xs font-bold text-emerald-700 uppercase tracking-wider">Staff</span>
                <span class="block font-medium text-slate-800 text-xs truncate">staff@tuksay.test</span>
                <span class="block text-[11px] text-slate-500">Pass: <code class="bg-slate-100 px-1 rounded">password</code></span>
            </button>
        </div>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>

    <script>
        function fillCredentials(email, password) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = password;
        }
    </script>
</x-guest-layout>
