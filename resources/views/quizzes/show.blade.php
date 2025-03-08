<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $quiz->title }}
            </h2>
            @if ($quiz->user_id === Auth::id())
                <div class="space-x-2">
                    <a href="{{ route('quizzes.edit', $quiz) }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                        編集
                    </a>
                    <form action="{{ route('quizzes.destroy', $quiz) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" onclick="return confirm('本当に削除しますか？')">
                            削除
                        </button>
                    </form>
                </div>
            @endif
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

                    <div class="mb-8">
                        <h3 class="text-lg font-medium mb-2">説明</h3>
                        <p class="text-gray-600">{{ $quiz->description ?: '説明はありません。' }}</p>
                    </div>

                    <div class="mb-4">
                        <h3 class="text-lg font-medium mb-4">問題一覧</h3>
                        <div class="space-y-6">
                            @foreach ($quiz->questions as $index => $question)
                                <div class="border rounded-lg p-6">
                                    <div class="flex justify-between items-start mb-4">
                                        <h4 class="text-lg font-medium">問題 {{ $index + 1 }}</h4>
                                        <span class="bg-blue-100 text-blue-800 text-sm font-medium px-2.5 py-0.5 rounded">
                                            {{ $question->points }}点
                                        </span>
                                    </div>
                                    <p class="text-gray-800 mb-4">{{ $question->question_text }}</p>
                                    
                                    <div class="ml-4 space-y-2">
                                        <h5 class="text-md font-medium mb-2">選択肢：</h5>
                                        @foreach ($question->options as $option)
                                            <div class="flex items-center gap-2">
                                                @if ($quiz->user_id === Auth::id())
                                                    <span class="inline-flex items-center justify-center w-5 h-5 {{ $option->is_correct ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} rounded-full text-xs">
                                                        {{ $option->is_correct ? '○' : '×' }}
                                                    </span>
                                                @endif
                                                <span class="text-gray-700">{{ $option->option_text }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    @if ($quiz->user_id !== Auth::id())
                        <div class="flex justify-center mt-8">
                            <a href="{{ route('quizzes.take', $quiz) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg">
                                このクイズに挑戦する
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 