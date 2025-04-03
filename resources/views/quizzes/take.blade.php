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

                    <div class="space-y-8">
                        @foreach ($quiz->questions as $index => $question)
                            <div class="border rounded-lg p-6 question-container" id="question-{{ $question->id }}" data-question-id="{{ $question->id }}">
                                <div class="flex justify-between items-start mb-4">
                                    <h4 class="text-lg font-medium">問題 {{ $index + 1 }}</h4>
                                    <span class="bg-blue-100 text-blue-800 text-sm font-medium px-2.5 py-0.5 rounded">
                                        {{ $question->points }}点
                                    </span>
                                </div>
                                
                                @if ($question->media_name)
                                <div class="mb-4">
                                    @php
                                        $isYoutubeUrl = preg_match('/youtube\.com|youtu\.be/', $question->media_url);
                                        $youtubeId = null;
                                        
                                        if ($isYoutubeUrl) {
                                            // YouTubeのURLからIDを抽出
                                            preg_match('/(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|\S*?[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $question->media_url, $matches);
                                            $youtubeId = $matches[1] ?? null;
                                        }
                                    @endphp
                                    
                                    @if ($isYoutubeUrl && $youtubeId)
                                        <div class="aspect-w-16 aspect-h-9">
                                            <iframe 
                                                src="https://www.youtube.com/embed/{{ $youtubeId }}" 
                                                class="w-full h-full max-h-96"
                                                frameborder="0" 
                                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                                allowfullscreen
                                            ></iframe>
                                        </div>
                                    @else
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
                                    @endif
                                </div>
                                @endif
                                
                                <p class="text-gray-800 mb-4">{{ $question->question_text }}</p>
                                
                                <div class="ml-4 space-y-2">
                                    <h5 class="text-md font-medium mb-2">選択肢：</h5>
                                    <div class="options-container">
                                        @foreach ($question->options as $option)
                                            <div class="flex items-center gap-2 mb-2 option-item">
                                                <input type="hidden" name="answers[{{ $question->id }}][{{ $option->id }}]" value="0">
                                                <input type="checkbox" 
                                                       name="answers[{{ $question->id }}][{{ $option->id }}]" 
                                                       value="1"
                                                       id="option_{{ $question->id }}_{{ $option->id }}"
                                                       data-option-id="{{ $option->id }}"
                                                       data-is-correct="{{ $option->is_correct ? 'true' : 'false' }}"
                                                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded option-checkbox">
                                                <label for="option_{{ $question->id }}_{{ $option->id }}" class="text-gray-700 option-label">
                                                    {{ $option->option_text }}
                                                </label>
                                                <span class="ml-2 hidden feedback-icon"></span>
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="mt-4">
                                        <button type="button" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded check-answer-btn">
                                            回答を確認
                                        </button>
                                    </div>

                                    <div class="mt-4 hidden result-container">
                                        <div class="correct-message hidden p-4 bg-green-100 text-green-800 rounded">
                                            <p class="font-bold">正解です！</p>
                                        </div>
                                        <div class="incorrect-message hidden p-4 bg-red-100 text-red-800 rounded">
                                            <p class="font-bold">不正解です。</p>
                                        </div>
                                        
                                        @if($question->explanation_text || $question->explanation_image_name)
                                            <button type="button" class="mt-4 text-blue-600 hover:text-blue-800 show-explanation-btn hidden">
                                                解説を表示
                                            </button>
                                            
                                            <div class="explanation-container mt-4 hidden p-4 bg-yellow-50 rounded-lg">
                                                <h5 class="font-medium text-lg mb-2">解説</h5>
                                                @if($question->explanation_text)
                                                    <p class="text-gray-700 mb-3">{{ $question->explanation_text }}</p>
                                                @endif
                                                
                                                @if($question->explanation_image_name)
                                                    <img src="{{ $question->explanation_image_url }}" alt="解説画像" class="max-w-full h-auto rounded">
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <form action="{{ route('quizzes.submit', $quiz) }}" method="POST" id="quiz-form">
                        @csrf
                        <div id="answers-container">
                            <!-- JavaScriptで回答データが追加されます -->
                        </div>
                        <div class="flex justify-center mt-8">
                            <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-lg">
                                回答を送信して結果を見る
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 回答確認ボタンのイベントリスナーを設定
            document.querySelectorAll('.check-answer-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const questionContainer = this.closest('.question-container');
                    const questionId = questionContainer.dataset.questionId;
                    const resultContainer = questionContainer.querySelector('.result-container');
                    const correctMessage = questionContainer.querySelector('.correct-message');
                    const incorrectMessage = questionContainer.querySelector('.incorrect-message');
                    const showExplanationBtn = questionContainer.querySelector('.show-explanation-btn');
                    
                    // 選択された回答をチェック
                    const optionItems = questionContainer.querySelectorAll('.option-item');
                    let isAllCorrect = true;
                    
                    optionItems.forEach(item => {
                        const checkbox = item.querySelector('.option-checkbox');
                        const isChecked = checkbox.checked;
                        const isCorrect = checkbox.dataset.isCorrect === 'true';
                        const feedbackIcon = item.querySelector('.feedback-icon');
                        
                        feedbackIcon.classList.remove('hidden');
                        
                        if (isChecked && isCorrect) {
                            // 正解を選択
                            feedbackIcon.innerHTML = '✅';
                            feedbackIcon.classList.add('text-green-600');
                        } else if (isChecked && !isCorrect) {
                            // 不正解を選択
                            feedbackIcon.innerHTML = '❌';
                            feedbackIcon.classList.add('text-red-600');
                            isAllCorrect = false;
                        } else if (!isChecked && isCorrect) {
                            // 正解を選択しなかった
                            feedbackIcon.innerHTML = '⚠️';
                            feedbackIcon.classList.add('text-yellow-600');
                            isAllCorrect = false;
                        } else {
                            // 不正解を選択しなかった (正しい)
                            feedbackIcon.innerHTML = '😶';
                            feedbackIcon.classList.add('text-gray-600');
                        }
                    });
                    
                    // 結果メッセージを表示
                    resultContainer.classList.remove('hidden');
                    if (isAllCorrect) {
                        correctMessage.classList.remove('hidden');
                        incorrectMessage.classList.add('hidden');
                    } else {
                        correctMessage.classList.add('hidden');
                        incorrectMessage.classList.remove('hidden');
                    }
                    
                    // 解説ボタンを表示
                    if (showExplanationBtn) {
                        showExplanationBtn.classList.remove('hidden');
                    }
                    
                    // このボタンを非表示にする
                    this.classList.add('hidden');
                });
            });
            
            // 解説表示ボタンのイベントリスナーを設定
            document.querySelectorAll('.show-explanation-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const explanationContainer = this.nextElementSibling;
                    explanationContainer.classList.remove('hidden');
                    this.classList.add('hidden');
                });
            });
            
            // フォーム送信時に回答データを収集
            document.getElementById('quiz-form').addEventListener('submit', function(e) {
                const answersContainer = document.getElementById('answers-container');
                answersContainer.innerHTML = '';
                
                document.querySelectorAll('.question-container').forEach(questionContainer => {
                    const questionId = questionContainer.dataset.questionId;
                    
                    questionContainer.querySelectorAll('.option-checkbox').forEach(checkbox => {
                        const optionId = checkbox.dataset.optionId;
                        const isChecked = checkbox.checked ? 1 : 0;
                        
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = `answers[${questionId}][${optionId}]`;
                        hiddenInput.value = isChecked;
                        
                        answersContainer.appendChild(hiddenInput);
                    });
                });
            });
        });
    </script>
    @endpush
</x-app-layout> 