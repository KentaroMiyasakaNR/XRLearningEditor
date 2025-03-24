<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="text-center mb-6">
        <h1 class="text-2xl font-bold text-gray-700 dark:text-gray-300">ログイン</h1>
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">アカウント情報を入力してください</p>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('メールアドレス')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="例：example@example.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('パスワード')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" 
                            placeholder="パスワードを入力してください" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" name="remember">
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">ログイン状態を保持する</span>
            </label>
        </div>

        <div class="flex flex-col mt-6 space-y-4">
            <x-primary-button class="w-full justify-center py-3 text-base">
                ログイン
            </x-primary-button>

            <div class="text-center text-sm text-gray-600 dark:text-gray-400">
                @if (Route::has('password.request'))
                    <a class="hover:text-gray-900 dark:hover:text-gray-100" href="{{ route('password.request') }}">
                        パスワードをお忘れですか？
                    </a>
                @endif
            </div>

            <div class="text-center text-sm text-gray-600 dark:text-gray-400 mt-2">
                <span>アカウントをお持ちでない方は</span>
                <a class="text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium" href="{{ route('register') }}">
                    新規登録
                </a>
            </div>
        </div>
    </form>
</x-guest-layout>
