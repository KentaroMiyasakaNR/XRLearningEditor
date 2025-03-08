<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $quiz->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if (session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    <div class="mb-8">
                        <h3 class="text-lg font-medium mb-2">説明</h3>
                        <p class="text-gray-600">{{ $quiz->description ?: '説明はありません。' }}</p>
                    </div>

                    <form action="{{ route('quizzes.submit', $quiz) }}" method="POST">
                        @csrf
                        <div class="space-y-8">
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
                                                <input type="hidden" name="answers[{{ $question->id }}][{{ $option->id }}]" value="0">
                                                <input type="checkbox" 
                                                       name="answers[{{ $question->id }}][{{ $option->id }}]" 
                                                       value="1"
                                                       id="option_{{ $question->id }}_{{ $option->id }}"
                                                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                                <label for="option_{{ $question->id }}_{{ $option->id }}" class="text-gray-700">
                                                    {{ $option->option_text }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="flex justify-center mt-8">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg">
                                回答を送信
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 