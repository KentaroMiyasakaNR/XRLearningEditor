<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('クイズ編集') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <!-- メディアファイル名登録ボタン -->
                    <div class="flex justify-end space-x-4 mb-4">
                        <button type="button" onclick="openMediaRegistrationModal('images')" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            画像ファイル名の登録
                        </button>
                        <button type="button" onclick="openMediaRegistrationModal('videos')" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                            動画ファイル名の登録
                        </button>
                    </div>

                    <!-- エラーメッセージ表示エリア -->
                    <div id="error-messages" class="mb-4 text-red-600 dark:text-red-400"></div>

                    <form action="{{ route('quizzes.update', $quiz) }}" method="POST" id="quizForm">
                        @csrf
                        @method('PUT')
                        <div class="mb-6">
                            <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">タイトル</label>
                            <input type="text" name="title" id="title" value="{{ $quiz->title }}" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500" required>
                        </div>

                        <div class="mb-6">
                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">説明</label>
                            <textarea name="description" id="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">{{ $quiz->description }}</textarea>
                        </div>

                        <div class="mb-6">
                            <label for="level" class="block text-sm font-medium text-gray-700 dark:text-gray-300">難易度</label>
                            <select name="level" id="level" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="S" {{ $quiz->level === 'S' ? 'selected' : '' }}>S級</option>
                                <option value="A" {{ $quiz->level === 'A' ? 'selected' : '' }}>A級</option>
                                <option value="B" {{ $quiz->level === 'B' ? 'selected' : '' }}>B級</option>
                                <option value="C" {{ $quiz->level === 'C' ? 'selected' : '' }}>C級</option>
                            </select>
                        </div>

                        <div id="questions-container">
                            @foreach($quiz->questions as $questionIndex => $question)
                            <div class="mb-6 p-6 border rounded-lg">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-medium">質問 {{ $questionIndex + 1 }}</h3>
                                    <button type="button" onclick="removeQuestion(this)" class="text-red-500 hover:text-red-700">
                                        削除
                                    </button>
                                </div>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">質問文</label>
                                    <input type="text" name="questions[{{ $questionIndex }}][question_text]" value="{{ $question->question_text }}" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500" required>
                                </div>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">配点</label>
                                    <input type="number" name="questions[{{ $questionIndex }}][points]" value="{{ $question->points }}" min="1" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500" required>
                                </div>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">メディアファイル</label>
                                    <div class="mt-1">
                                        <select name="questions[{{ $questionIndex }}][media_name]" class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="">なし</option>
                                            <!-- 動画ファイルがここに動的に追加されます -->
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">解説テキスト</label>
                                    <textarea name="questions[{{ $questionIndex }}][explanation_text]" rows="3" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">{{ $question->explanation_text ?? '' }}</textarea>
                                </div>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">解説画像</label>
                                    <div class="mt-1">
                                        <select name="questions[{{ $questionIndex }}][explanation_image_name]" class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="">なし</option>
                                            <!-- 画像ファイルがここに動的に追加されます -->
                                        </select>
                                    </div>
                                </div>
                                <div class="options-container">
                                    <h4 class="text-md font-medium mb-2">選択肢：</h4>
                                    <div class="space-y-2">
                                        @foreach($question->options as $optionIndex => $option)
                                        <div class="flex items-center gap-4">
                                            <input type="text" name="questions[{{ $questionIndex }}][options][{{ $optionIndex }}][option_text]" value="{{ $option->option_text }}" placeholder="選択肢{{ $optionIndex + 1 }}" class="flex-1 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500" required>
                                            <input type="hidden" name="questions[{{ $questionIndex }}][options][{{ $optionIndex }}][is_correct]" value="0">
                                            <input type="checkbox" name="questions[{{ $questionIndex }}][options][{{ $optionIndex }}][is_correct]" value="1" {{ $option->is_correct ? 'checked' : '' }} class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                            <label class="text-sm text-gray-600 dark:text-gray-400">正解</label>
                                            <select name="questions[{{ $questionIndex }}][options][{{ $optionIndex }}][next_quiz_id]" class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                                <option value="">次のクイズなし</option>
                                                <!-- 既存のクイズがここに動的に追加されます -->
                                            </select>
                                            @if($optionIndex >= 2)
                                            <button type="button" onclick="removeOption(this)" class="text-red-500 hover:text-red-700">削除</button>
                                            @endif
                                        </div>
                                        @endforeach
                                    </div>
                                    <button type="button" onclick="addOption(this, {{ $questionIndex }})" class="mt-2 text-sm text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300">
                                        + 選択肢を追加
                                    </button>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <div class="flex justify-between items-center mb-6">
                            <button type="button" onclick="addQuestionToForm()" style="background-color: #047857;" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                質問を追加
                            </button>

                            <div class="space-x-4">
                                <a href="{{ route('quizzes.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    キャンセル
                                </a>
                                <button type="submit" style="background-color: #2563eb;" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    更新
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let questionCount = {{ count($quiz->questions) }};
        let existingQuizzes = []; // 既存のクイズを保持する配列
        let mediaFiles = { videos: [], images: [] };

        // 既存のクイズを取得
        async function fetchExistingQuizzes() {
            try {
                // APIエンドポイントからクイズデータを取得
                const response = await fetch('{{ url('/api/quizzes') }}');
                const data = await response.json();
                existingQuizzes = data;
                updateNextQuizSelects();
            } catch (error) {
                console.error('クイズの取得に失敗しました:', error);
                // エラーが発生した場合は空の配列を使用
                existingQuizzes = [];
                updateNextQuizSelects();
            }
        }

        // 次のクイズ選択肢を更新
        function updateNextQuizSelects() {
            const selects = document.querySelectorAll('select[name$="[next_quiz_id]"]');
            selects.forEach(select => {
                const currentValue = select.value;
                select.innerHTML = '<option value="">次のクイズなし</option>';
                existingQuizzes.forEach(quiz => {
                    const option = document.createElement('option');
                    option.value = quiz.id;
                    option.textContent = quiz.title;
                    select.appendChild(option);
                });
                if (currentValue) {
                    select.value = currentValue;
                }
            });
        }

        // メディアファイルを取得
        async function fetchMediaFiles() {
            try {
                const response = await fetch('{{ route('media.index') }}');
                
                // レスポンスステータスをチェック
                if (!response.ok && response.status !== 200) {
                    throw new Error(`サーバーエラー：HTTPステータス ${response.status}`);
                }
                
                // レスポンスのContent-Typeをチェック
                const contentType = response.headers.get("content-type");
                if (contentType && contentType.indexOf("application/json") === -1) {
                    console.warn("JSONではないレスポンスを受信しました。エラー回復を試みます。");
                    // JSONではない場合は空の結果を使用
                    mediaFiles = { videos: [], images: [] };
                    updateMediaSelects();
                    return;
                }
                
                const data = await response.json();
                
                // APIがエラーオブジェクトを返した場合でも動画と画像の配列が含まれていることを確認
                if (data.videos !== undefined && data.images !== undefined) {
                    mediaFiles = data;
                } else {
                    // データ形式が期待通りでない場合は、空の配列を使用
                    console.warn("予期しないデータ形式を受信しました", data);
                    mediaFiles = { 
                        videos: Array.isArray(data.videos) ? data.videos : [], 
                        images: Array.isArray(data.images) ? data.images : []
                    };
                }
                updateMediaSelects();
            } catch (error) {
                console.error('メディアファイルの取得に失敗しました:', error);
                // エラーが発生しても続行できるよう、空のオブジェクトを設定
                mediaFiles = { videos: [], images: [] };
                updateMediaSelects();
            }
        }

        // メディア選択肢を更新
        function updateMediaSelects() {
            const videoSelects = document.querySelectorAll('select[name$="[media_name]"]');
            const imageSelects = document.querySelectorAll('select[name$="[explanation_image_name]"]');

            videoSelects.forEach(select => {
                const currentValue = select.value;
                select.innerHTML = '<option value="">なし</option>';
                mediaFiles.videos.forEach(video => {
                    const option = document.createElement('option');
                    option.value = video.filename;
                    option.textContent = video.title || video.filename;
                    select.appendChild(option);
                });
                if (currentValue) {
                    select.value = currentValue;
                }
            });

            imageSelects.forEach(select => {
                const currentValue = select.value;
                select.innerHTML = '<option value="">なし</option>';
                mediaFiles.images.forEach(image => {
                    const option = document.createElement('option');
                    option.value = image.filename;
                    option.textContent = image.title || image.filename;
                    select.appendChild(option);
                });
                if (currentValue) {
                    select.value = currentValue;
                }
            });
        }

        function addQuestionToForm() {
            const container = document.getElementById('questions-container');
            const questionDiv = document.createElement('div');
            questionDiv.className = 'mb-6 p-6 border rounded-lg';
            
            const currentCount = questionCount + 1;
            questionDiv.innerHTML = `
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium">質問 ${currentCount}</h3>
                    <button type="button" onclick="removeQuestion(this)" class="text-red-500 hover:text-red-700">
                        削除
                    </button>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">質問文</label>
                    <input type="text" name="questions[${questionCount}][question_text]" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">配点</label>
                    <input type="number" name="questions[${questionCount}][points]" value="1" min="1" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">メディアファイル</label>
                    <div class="mt-1">
                        <select name="questions[${questionCount}][media_name]" class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">なし</option>
                            <!-- 動画ファイルがここに動的に追加されます -->
                        </select>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">解説テキスト</label>
                    <textarea name="questions[${questionCount}][explanation_text]" rows="3" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">解説画像</label>
                    <div class="mt-1">
                        <select name="questions[${questionCount}][explanation_image_name]" class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">なし</option>
                            <!-- 画像ファイルがここに動的に追加されます -->
                        </select>
                    </div>
                </div>
                <div class="options-container">
                    <h4 class="text-md font-medium mb-2">選択肢：</h4>
                    <div class="space-y-2">
                        <div class="flex items-center gap-4">
                            <input type="text" name="questions[${questionCount}][options][0][option_text]" placeholder="選択肢1" class="flex-1 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500" required>
                            <input type="hidden" name="questions[${questionCount}][options][0][is_correct]" value="0">
                            <input type="checkbox" name="questions[${questionCount}][options][0][is_correct]" value="1" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label class="text-sm text-gray-600 dark:text-gray-400">正解</label>
                            <select name="questions[${questionCount}][options][0][next_quiz_id]" class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">次のクイズなし</option>
                                <!-- 既存のクイズがここに動的に追加されます -->
                            </select>
                        </div>
                        <div class="flex items-center gap-4">
                            <input type="text" name="questions[${questionCount}][options][1][option_text]" placeholder="選択肢2" class="flex-1 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500" required>
                            <input type="hidden" name="questions[${questionCount}][options][1][is_correct]" value="0">
                            <input type="checkbox" name="questions[${questionCount}][options][1][is_correct]" value="1" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label class="text-sm text-gray-600 dark:text-gray-400">正解</label>
                            <select name="questions[${questionCount}][options][1][next_quiz_id]" class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">次のクイズなし</option>
                                <!-- 既存のクイズがここに動的に追加されます -->
                            </select>
                        </div>
                    </div>
                    <button type="button" onclick="addOption(this, ${questionCount})" class="mt-2 text-sm text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300">
                        + 選択肢を追加
                    </button>
                </div>
            `;
            container.appendChild(questionDiv);
            questionCount++;
            
            // 新しい質問に対してメディア選択肢と次のクイズ選択肢を更新
            updateMediaSelects();
            updateNextQuizSelects();
        }

        function removeQuestion(button) {
            const questionDiv = button.closest('.mb-6');
            if (questionDiv) {
                questionDiv.remove();
            }
        }

        function addOption(button, questionIndex) {
            const optionsContainer = button.previousElementSibling;
            const optionCount = optionsContainer.children.length;
            const optionDiv = document.createElement('div');
            optionDiv.className = 'flex items-center gap-4';
            optionDiv.innerHTML = `
                <input type="text" name="questions[${questionIndex}][options][${optionCount}][option_text]" placeholder="選択肢${optionCount + 1}" class="flex-1 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500" required>
                <input type="hidden" name="questions[${questionIndex}][options][${optionCount}][is_correct]" value="0">
                <input type="checkbox" name="questions[${questionIndex}][options][${optionCount}][is_correct]" value="1" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                <label class="text-sm text-gray-600 dark:text-gray-400">正解</label>
                <select name="questions[${questionIndex}][options][${optionCount}][next_quiz_id]" class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">次のクイズなし</option>
                    ${existingQuizzes.map(quiz => `<option value="${quiz.id}">${quiz.title}</option>`).join('')}
                </select>
                <button type="button" onclick="removeOption(this)" class="text-red-500 hover:text-red-700">削除</button>
            `;
            optionsContainer.appendChild(optionDiv);
        }

        function removeOption(button) {
            const optionDiv = button.closest('.flex');
            if (optionDiv) {
                optionDiv.remove();
            }
        }

        // メディアファイル名登録モーダルを開く
        function openMediaRegistrationModal(type) {
            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50';
            modal.innerHTML = `
                <div class="relative top-20 mx-auto p-6 border w-96 shadow-lg rounded-lg bg-white dark:bg-gray-800">
                    <div class="mt-3">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                ${type === 'videos' ? '動画' : '画像'}ファイル名の登録
                            </h3>
                            <button type="button" onclick="this.closest('.fixed').remove()" class="text-gray-400 hover:text-gray-500">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        <div class="mt-2">
                            <form id="mediaUploadForm" class="space-y-4">
                                <div class="space-y-4">
                                    <div>
                                        <label for="file_select" class="block text-sm font-medium text-gray-700 dark:text-gray-300">ファイルを選択 <span class="text-red-500">*</span></label>
                                        <input type="file" name="file_select" id="file_select" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500" accept="${type === 'videos' ? 'video/*' : 'image/*'}" required multiple>
                                        <p class="mt-1 text-xs text-gray-500">
                                            ${type === 'videos' ? '動画ファイルを選択してください。複数選択可能です。ファイル名だけが取得されます。' : '画像ファイルを選択してください。複数選択可能です。ファイル名だけが取得されます。'}
                                        </p>
                                    </div>

                                    <div>
                                        <label for="filename" class="block text-sm font-medium text-gray-700 dark:text-gray-300">ファイル名 <span class="text-red-500">*</span></label>
                                        <input type="text" name="filename" id="filename" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500" placeholder="${type === 'videos' ? '例: sample_video.mp4' : '例: sample_image.jpg'}" required readonly>
                                        <p class="mt-1 text-xs text-gray-500">
                                            ${type === 'videos' ? '選択した動画ファイルの名前が自動的に入力されます。Unity側でこの名前が参照されます。' : '選択した画像ファイルの名前が自動的に入力されます。'}
                                        </p>
                                    </div>
                                    
                                    <div>
                                        <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">タイトル</label>
                                        <input type="text" name="title" id="title" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500" placeholder="表示用のタイトル（省略可）">
                                    </div>
                                    
                                    <div>
                                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">説明</label>
                                        <textarea name="description" id="description" rows="2" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500" placeholder="メディアの説明（省略可）"></textarea>
                                    </div>
                                    
                                    <input type="hidden" name="type" value="${type}">
                                </div>
                                
                                <div class="flex justify-end space-x-3 mt-2">
                                    <button type="button" onclick="this.closest('.fixed').remove()" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700">
                                        キャンセル
                                    </button>
                                    <button type="submit" id="uploadButton" class="px-4 py-2 text-sm font-medium text-blue-700 bg-white dark:bg-gray-800 border-2 border-blue-600 dark:border-blue-500 rounded-md disabled:opacity-50 disabled:cursor-not-allowed">
                                        登録
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);

            const form = modal.querySelector('#mediaUploadForm');
            const uploadButton = modal.querySelector('#uploadButton');
            const fileInput = modal.querySelector('#file_select');
            const filenameInput = modal.querySelector('#filename');
            const titleInput = modal.querySelector('#title');

            // ファイル選択時にファイル名を自動入力
            fileInput.addEventListener('change', function(e) {
                if (this.files && this.files.length > 0) {
                    // 複数のファイルが選択された場合の処理
                    if (this.files.length > 1) {
                        // 選択されたファイルの数を表示
                        const fileCount = this.files.length;
                        filenameInput.value = `${fileCount}個のファイルを選択中`;
                        
                        // 選択中のファイル情報を表示するためのコンテナを作成または取得
                        let filesListContainer = modal.querySelector('.selected-files-list');
                        if (!filesListContainer) {
                            filesListContainer = document.createElement('div');
                            filesListContainer.className = 'selected-files-list mt-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-md max-h-32 overflow-y-auto';
                            filenameInput.parentNode.appendChild(filesListContainer);
                        }
                        
                        // 選択されたファイルのリストを表示
                        filesListContainer.innerHTML = '<p class="text-xs font-medium mb-1">選択されたファイル:</p>';
                        const filesList = document.createElement('ul');
                        filesList.className = 'text-xs space-y-1';
                        
                        // 各ファイル名をリストに追加
                        for (let i = 0; i < this.files.length; i++) {
                            const fileName = this.files[i].name;
                            const listItem = document.createElement('li');
                            listItem.className = 'flex items-center justify-between';
                            
                            const fileNameSpan = document.createElement('span');
                            fileNameSpan.textContent = fileName;
                            fileNameSpan.className = 'truncate';
                            listItem.appendChild(fileNameSpan);
                            
                            const addButton = document.createElement('button');
                            addButton.type = 'button';
                            addButton.textContent = '登録';
                            addButton.className = 'ml-2 text-xs text-blue-600 hover:text-blue-800';
                            addButton.dataset.filename = fileName;
                            addButton.onclick = function() {
                                filenameInput.value = this.dataset.filename;
                                
                                // タイトルが未入力の場合、ファイル名から自動生成
                                if (!titleInput.value) {
                                    const titleWithoutExtension = this.dataset.filename.split('.').slice(0, -1).join('.');
                                    titleInput.value = titleWithoutExtension
                                        .replace(/_/g, ' ')
                                        .replace(/-/g, ' ')
                                        .replace(/\b\w/g, l => l.toUpperCase());
                                }
                            };
                            listItem.appendChild(addButton);
                            
                            filesList.appendChild(listItem);
                        }
                        
                        filesListContainer.appendChild(filesList);
                        
                        // ファイル選択中はファイル名を手動編集できるようにする
                        filenameInput.readOnly = false;
                    } else {
                        // 単一ファイルの場合は従来の処理
                        const filename = this.files[0].name;
                        filenameInput.value = filename;
                        
                        // ファイルリストが表示されていれば削除
                        const filesListContainer = modal.querySelector('.selected-files-list');
                        if (filesListContainer) {
                            filesListContainer.remove();
                        }
                        
                        // タイトルが未入力の場合は、拡張子を除いたファイル名をデフォルトのタイトルとして設定
                        if (!titleInput.value) {
                            const titleWithoutExtension = filename.split('.').slice(0, -1).join('.');
                            // スネークケースやケバブケースをスペースに変換してタイトルっぽく
                            titleInput.value = titleWithoutExtension
                                .replace(/_/g, ' ')
                                .replace(/-/g, ' ')
                                .replace(/\b\w/g, l => l.toUpperCase()); // 単語の先頭を大文字に
                        }
                        
                        // 単一ファイルの場合は読み取り専用に
                        filenameInput.readOnly = true;
                    }
                }
            });

            // フォーム送信のハンドリング
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                // フォームデータを取得
                const formData = new FormData(this);
                
                // ボタンを無効化
                uploadButton.disabled = true;
                uploadButton.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-blue-700 inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    登録中...
                `;
                
                try {
                    // 複数ファイルが選択されている場合
                    if (fileInput.files && fileInput.files.length > 1) {
                        const files = fileInput.files;
                        const type = formData.get('type');
                        const description = formData.get('description');
                        let successCount = 0;
                        let errorMessages = [];
                        
                        // プログレスバーを表示
                        const progressContainer = document.createElement('div');
                        progressContainer.className = 'mt-4';
                        progressContainer.innerHTML = `
                            <p class="text-sm mb-1">ファイル登録中... <span id="progress-text">0/${files.length}</span></p>
                            <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                                <div id="progress-bar" class="bg-blue-600 h-2.5 rounded-full" style="width: 0%"></div>
                            </div>
                        `;
                        form.appendChild(progressContainer);
                        
                        const progressBar = progressContainer.querySelector('#progress-bar');
                        const progressText = progressContainer.querySelector('#progress-text');

                        // ファイルごとに個別に登録リクエストを送信
                        for (let i = 0; i < files.length; i++) {
                            const fileName = files[i].name;
                            
                            // ファイル名から自動的にタイトルを生成（拡張子を除去）
                            const titleWithoutExtension = fileName.split('.').slice(0, -1).join('.');
                            const autoTitle = titleWithoutExtension
                                .replace(/_/g, ' ')
                                .replace(/-/g, ' ')
                                .replace(/\b\w/g, l => l.toUpperCase());
                            
                            try {
                                const response = await fetch('{{ route('media.store') }}', {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                        'Accept': 'application/json',
                                        'Content-Type': 'application/json'
                                    },
                                    body: JSON.stringify({
                                        filename: fileName,
                                        type: type,
                                        title: autoTitle,  // ファイル名から自動生成したタイトルを使用
                                        description: description
                                    })
                                });
                                
                                // レスポンスの処理
                                if (response.ok) {
                                    successCount++;
                                } else {
                                    const errorData = await response.json();
                                    console.log(`エラー詳細 (${fileName}):`, errorData); // エラー詳細をコンソールに出力
                                    
                                    if (errorData.errors) {
                                        const errors = Object.values(errorData.errors).flat();
                                        errorMessages.push(`${fileName}: ${errors.join(', ')}`);
                                    } else if (errorData.message) {
                                        errorMessages.push(`${fileName}: ${errorData.message}`);
                                    } else {
                                        errorMessages.push(`${fileName}: 登録に失敗しました`);
                                    }
                                }
                            } catch (error) {
                                errorMessages.push(`${fileName}: ${error.message}`);
                            }
                            
                            // プログレスバーを更新
                            const progress = Math.round(((i + 1) / files.length) * 100);
                            progressBar.style.width = `${progress}%`;
                            progressText.textContent = `${i + 1}/${files.length}`;
                        }
                        
                        // 成功時、メディアファイルリストを更新
                        await fetchMediaFiles();
                        
                        // モーダルを閉じる
                        modal.remove();
                        
                        // 結果を通知
                        if (successCount > 0) {
                            const successNotification = document.createElement('div');
                            successNotification.className = 'fixed bottom-4 right-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-md max-w-md overflow-auto';
                            
                            let message = '';
                            if (errorMessages.length > 0) {
                                message = `<p class="text-sm">${successCount}個のファイルが登録されました。${errorMessages.length}個は失敗しました。</p>`;
                            } else {
                                message = `<p class="text-sm">全${successCount}個のファイルが正常に登録されました。</p>`;
                            }
                            
                            successNotification.innerHTML = `
                                <div class="flex">
                                    <div class="py-1"><svg class="h-6 w-6 text-green-500 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg></div>
                                    <div>
                                        <p class="font-bold">登録完了</p>
                                        ${message}
                                    </div>
                                </div>
                            `;
                            document.body.appendChild(successNotification);
                            
                            // エラーがあれば別途通知
                            if (errorMessages.length > 0) {
                                const errorNotification = document.createElement('div');
                                errorNotification.className = 'fixed bottom-4 left-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-md max-w-md max-h-64 overflow-auto';
                                
                                let errorList = '<ul class="list-disc list-inside text-xs mt-1">';
                                errorMessages.forEach(msg => {
                                    errorList += `<li>${msg}</li>`;
                                });
                                errorList += '</ul>';
                                
                                errorNotification.innerHTML = `
                                    <div class="flex">
                                        <div class="py-1"><svg class="h-6 w-6 text-red-500 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg></div>
                                        <div>
                                            <p class="font-bold">エラーが発生しました</p>
                                            <p class="text-sm">以下のファイルの登録に失敗しました：</p>
                                            ${errorList}
                                        </div>
                                    </div>
                                `;
                                document.body.appendChild(errorNotification);
                                
                                // 8秒後にエラー通知を消す（成功通知より長く表示）
                                setTimeout(() => {
                                    errorNotification.remove();
                                }, 8000);
                            }
                            
                            // 5秒後に成功通知を消す
                            setTimeout(() => {
                                successNotification.remove();
                            }, 5000);
                        } else if (errorMessages.length > 0) {
                            // 全て失敗した場合
                            const errorNotification = document.createElement('div');
                            errorNotification.className = 'fixed bottom-4 right-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-md max-w-md max-h-64 overflow-auto';
                            
                            let errorList = '<ul class="list-disc list-inside text-xs mt-1">';
                            errorMessages.forEach(msg => {
                                errorList += `<li>${msg}</li>`;
                            });
                            errorList += '</ul>';
                            
                            errorNotification.innerHTML = `
                                <div class="flex">
                                    <div class="py-1"><svg class="h-6 w-6 text-red-500 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg></div>
                                    <div>
                                        <p class="font-bold">登録失敗</p>
                                        <p class="text-sm">全てのファイルの登録に失敗しました：</p>
                                        ${errorList}
                                    </div>
                                </div>
                            `;
                            document.body.appendChild(errorNotification);
                            
                            // 8秒後に通知を消す
                            setTimeout(() => {
                                errorNotification.remove();
                            }, 8000);
                        }
                    } else {
                        // 単一ファイルの場合は従来の処理
                        const response = await fetch('{{ route('media.store') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                filename: formData.get('filename'),
                                type: formData.get('type'),
                                title: formData.get('title'),
                                description: formData.get('description')
                            })
                        });

                        // Content-Typeをチェック
                        const contentType = response.headers.get("content-type");
                        if (contentType && contentType.indexOf("application/json") === -1) {
                            // エラーメッセージをより具体的に
                            const errorText = await response.text();
                            let errorMessage = "サーバーエラーが発生しました。詳細: ";
                            
                            // HTMLからエラーメッセージを抽出しようと試みる
                            if (errorText.includes("<title>")) {
                                const titleMatch = errorText.match(/<title>(.*?)<\/title>/);
                                if (titleMatch && titleMatch[1]) {
                                    errorMessage += titleMatch[1];
                                }
                            } else {
                                // HTML以外の応答の場合
                                errorMessage += "応答タイプが予期しないものでした";
                            }
                            
                            console.error('非JSONレスポンス:', errorText.substring(0, 500) + '...');
                            throw new Error(errorMessage);
                        }
                        
                        let data;
                        try {
                            data = await response.json();
                        } catch (jsonError) {
                            throw new Error("レスポンスのJSONパースに失敗しました。サーバーエラーの可能性があります。");
                        }
                        
                        if (response.ok) {
                            // 成功時、メディアファイルリストを更新
                            await fetchMediaFiles();
                            // モーダルを閉じる
                            modal.remove();
                            
                            // 成功メッセージを表示
                            const successNotification = document.createElement('div');
                            successNotification.className = 'fixed bottom-4 right-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-md';
                            successNotification.innerHTML = `
                                <div class="flex">
                                    <div class="py-1"><svg class="h-6 w-6 text-green-500 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg></div>
                                    <div>
                                        <p class="font-bold">登録成功</p>
                                        <p class="text-sm">${formData.get('filename')} が正常に登録されました。</p>
                                    </div>
                                </div>
                            `;
                            document.body.appendChild(successNotification);
                            
                            // 3秒後に通知を消す
                            setTimeout(() => {
                                successNotification.remove();
                            }, 3000);
                        } else {
                            // エラーメッセージをより詳細に表示
                            console.log('エラー詳細:', data); // エラー詳細をコンソールに出力
                            
                            if (data.errors) {
                                const errorMessages = [];
                                for (const [field, messages] of Object.entries(data.errors)) {
                                    errorMessages.push(`${field}: ${messages.join(', ')}`);
                                }
                                throw new Error(errorMessages.join('\n'));
                            } else if (data.message) {
                                throw new Error(data.message);
                            } else {
                                throw new Error('登録に失敗しました。');
                            }
                        }
                    }
                } catch (error) {
                    // エラーメッセージをモーダル内に表示
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'text-red-500 text-sm mt-2 bg-red-50 p-2 rounded';
                    errorDiv.textContent = error.message;
                    
                    // エラーの詳細をコンソールに記録
                    console.error('登録エラー:', error);
                    
                    // 既存のエラーメッセージがあれば削除
                    const existingError = modal.querySelector('.text-red-500');
                    if (existingError) {
                        existingError.remove();
                    }
                    
                    // フォームの上に表示
                    form.parentNode.insertBefore(errorDiv, form);
                    
                    uploadButton.disabled = false;
                    uploadButton.textContent = '登録';
                }
            });
        }

        // フォーム送信のハンドリング
        document.getElementById('quizForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const form = e.target;
            const errorDiv = document.getElementById('error-messages');
            errorDiv.innerHTML = ''; // エラーメッセージをクリア

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form),
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    }
                });

                const result = await response.json();

                if (!response.ok) {
                    // バリデーションエラーの処理
                    if (response.status === 422) {
                        const errors = result.errors;
                        let errorHtml = '<ul class="list-disc list-inside">';
                        
                        Object.keys(errors).forEach(key => {
                            errors[key].forEach(error => {
                                errorHtml += `<li>${error}</li>`;
                            });
                        });
                        
                        errorHtml += '</ul>';
                        errorDiv.innerHTML = errorHtml;
                        
                        // エラーメッセージまでスクロール
                        errorDiv.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    } else {
                        throw new Error('送信に失敗しました');
                    }
                } else {
                    // 成功時の処理
                    window.location.href = result.redirect || '/quizzes';
                }
            } catch (error) {
                errorDiv.innerHTML = `<p>${error.message}</p>`;
                errorDiv.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });

        // DOM読み込み完了時の処理
        document.addEventListener('DOMContentLoaded', function() {
            fetchExistingQuizzes();
            fetchMediaFiles();
        });
    </script>
    @endpush
</x-app-layout> 