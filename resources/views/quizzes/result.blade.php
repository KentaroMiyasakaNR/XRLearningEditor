<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $quiz->title }} - 結果
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-8 text-center">
                        <h3 class="text-2xl font-bold mb-4">採点結果</h3>
                        <div class="text-4xl font-bold {{ $earnedPoints === $totalPoints ? 'text-green-600' : 'text-blue-600' }}">
                            {{ $earnedPoints }} / {{ $totalPoints }}点
                        </div>
                        <div class="mt-2 text-gray-600">
                            正答率: {{ round(($earnedPoints / $totalPoints) * 100, 1) }}%
                        </div>
                    </div>

                    <div class="space-y-8">
                        @foreach ($quiz->questions as $question)
                            @php
                                $result = $results[$question->id];
                                $isCorrect = $result['is_correct'];
                            @endphp
                            <div class="border rounded-lg p-6 {{ $isCorrect ? 'bg-green-50' : 'bg-red-50' }}">
                                <div class="flex justify-between items-start mb-4">
                                    <h4 class="text-lg font-medium">{{ $question->question_text }}</h4>
                                    <div class="text-right">
                                        <span class="bg-blue-100 text-blue-800 text-sm font-medium px-2.5 py-0.5 rounded">
                                            {{ $result['earned_points'] }}/{{ $question->points }}点
                                        </span>
                                    </div>
                                </div>
                                
                                @if ($question->media_name)
                                <div class="mb-4">
                                    <video 
                                        src="{{ $question->media_url }}" 
                                        class="w-full max-h-96 object-contain" 
                                        controls
                                        controlsList="nodownload"
                                        preload="metadata"
                                        playsinline
                                        oncontextmenu="return false;"
                                    >
                                        お使いのブラウザは動画再生をサポートしていません。
                                    </video>
                                </div>
                                @endif
                                
                                <div class="ml-4 space-y-2">
                                    <h5 class="text-md font-medium mb-2">選択肢：</h5>
                                    @foreach ($question->options as $option)
                                        @php
                                            $optionResult = $result['options'][$option->id];
                                            $isCorrect = $optionResult['is_correct'];
                                            $userAnswer = $optionResult['user_answer'];
                                        @endphp
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex items-center justify-center w-5 h-5 rounded-full text-xs
                                                {{ $isCorrect && $userAnswer ? 'bg-green-100 text-green-800' : '' }}
                                                {{ $isCorrect && !$userAnswer ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                {{ !$isCorrect && $userAnswer ? 'bg-red-100 text-red-800' : '' }}
                                                {{ !$isCorrect && !$userAnswer ? 'bg-gray-100 text-gray-800' : '' }}">
                                                @if ($isCorrect && $userAnswer)
                                                    ◎
                                                @elseif ($isCorrect && !$userAnswer)
                                                    ○
                                                @elseif (!$isCorrect && $userAnswer)
                                                    ×
                                                @else
                                                    -
                                                @endif
                                            </span>
                                            <span class="text-gray-700">{{ $option->option_text }}</span>
                                        </div>
                                    @endforeach
                                </div>
                                
                                @if($question->explanation_text || $question->explanation_image_name)
                                <div class="mt-4">
                                    <button type="button" class="text-blue-600 hover:text-blue-800 show-explanation-btn" data-target="explanation-{{ $question->id }}">
                                        解説を表示
                                    </button>
                                    
                                    <div id="explanation-{{ $question->id }}" class="explanation-container mt-4 hidden p-4 bg-yellow-50 rounded-lg">
                                        <h5 class="font-medium text-lg mb-2">解説</h5>
                                        @if($question->explanation_text)
                                            <p class="text-gray-700 mb-3">{{ $question->explanation_text }}</p>
                                        @endif
                                        
                                        @if($question->explanation_image_name)
                                            <img src="{{ $question->explanation_image_url }}" alt="解説画像" class="max-w-full h-auto rounded">
                                        @endif
                                    </div>
                                </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <div class="flex justify-center gap-4 mt-8">
                        <a href="{{ route('quizzes.show', $quiz) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded-lg">
                            クイズの詳細に戻る
                        </a>
                        <a href="{{ route('quizzes.index') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg">
                            クイズ一覧に戻る
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 解説表示ボタンのイベントリスナーを設定
            document.querySelectorAll('.show-explanation-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const targetId = this.dataset.target;
                    const explanationContainer = document.getElementById(targetId);
                    explanationContainer.classList.toggle('hidden');
                    
                    if (explanationContainer.classList.contains('hidden')) {
                        this.textContent = '解説を表示';
                    } else {
                        this.textContent = '解説を隠す';
                    }
                });
            });
        });
    </script>
    @endpush
</x-app-layout> 