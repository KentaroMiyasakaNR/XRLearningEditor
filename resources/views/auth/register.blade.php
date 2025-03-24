<x-guest-layout>
    <div class="text-center mb-6">
        <h1 class="text-2xl font-bold text-gray-700 dark:text-gray-300">アカウント登録</h1>
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">必要情報を入力して新規登録してください</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('氏名')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="例：山田 太郎" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('メールアドレス')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="例：example@example.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('パスワード')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password"
                            placeholder="8文字以上で入力してください" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('パスワード（確認用）')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password"
                            placeholder="同じパスワードを再入力してください" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex flex-col mt-6 space-y-4">
            <x-primary-button class="w-full justify-center py-3 text-base">
                登録する
            </x-primary-button>

            <div class="text-center text-sm text-gray-600 dark:text-gray-400 mt-2">
                <span>すでにアカウントをお持ちの方は</span>
                <a class="text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium" href="{{ route('login') }}">
                    ログイン
                </a>
            </div>
        </div>
    </form>
</x-guest-layout>
