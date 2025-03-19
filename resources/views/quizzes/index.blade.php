<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('クイズ一覧') }}
            </h2>
            <a href="{{ route('quizzes.manage') }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                クイズ管理
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach ($quizzes as $quiz)
                            <div class="border rounded-lg p-4 hover:shadow-lg transition-shadow">
                                <h3 class="text-lg font-semibold mb-2">{{ $quiz->title }}</h3>
                                <p class="text-gray-600 mb-4">{{ Str::limit($quiz->description, 100) }}</p>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-500">
                                        {{ $quiz->questions->count() }}問
                                    </span>
                                    <div class="space-x-2">
                                        <a href="{{ route('quizzes.show', $quiz) }}" class="text-blue-500 hover:text-blue-700">
                                            詳細
                                        </a>
                                        <a href="{{ route('quizzes.take', $quiz) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded text-sm">
                                            受験する
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4">
                        {{ $quizzes->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 