<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'XRLearningEditor') }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
        <link rel="alternate icon" href="{{ asset('favicon.ico') }}">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link href="https://fonts.bunny.net/css?family=noto-sans-jp:400,500,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-b from-blue-50 to-indigo-100 dark:from-gray-900 dark:to-gray-800">
            <div class="mb-2">
                <a href="/" class="flex flex-col items-center">
                    <x-application-logo class="w-24 h-24 fill-current text-indigo-600 dark:text-indigo-400" />
                    <span class="mt-2 text-xl font-bold text-indigo-600 dark:text-indigo-400">XRラーニングエディタ</span>
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-4 px-6 py-6 bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
                {{ $slot }}
            </div>
            
            <div class="mt-6 text-center text-sm text-gray-600 dark:text-gray-400">
                &copy; {{ date('Y') }} XRLearningEditor - すべての権利を保有
            </div>
        </div>
    </body>
</html>
