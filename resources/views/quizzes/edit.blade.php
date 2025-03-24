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

                    <!-- メディア通知メッセージ -->
                    <div id="media-notification" class="hidden mb-4 p-4 border-l-4 border-yellow-500 bg-yellow-50 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-200">
                        <div class="flex justify-between items-center">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-yellow-600 dark:text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="font-medium">
                                        メディアファイルがまだ登録されていません。
                                    </p>
                                    <p class="mt-1 text-sm">
                                        「画像ファイル名の登録」または「動画ファイル名の登録」ボタンをクリックして、まずメディアファイルの情報を登録してください。メディアファイルを登録すると、ドロップダウンメニューにファイルが表示されるようになります。
                                    </p>
                                </div>
                            </div>
                            <div class="ml-4">
                                <button type="button" onclick="refreshMediaFiles()" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-yellow-700 bg-yellow-100 hover:bg-yellow-200 dark:bg-yellow-800 dark:text-yellow-200 dark:hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                                    <svg class="mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    メディアリストを更新
                                </button>
                            </div>
                        </div>
                    </div>

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
                                            @if ($question->media_name)
                                            <option value="{{ $question->media_name }}" selected>{{ $question->media_name }}</option>
                                            @endif
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
                                            @if ($question->explanation_image_name)
                                            <option value="{{ $question->explanation_image_name }}" selected>{{ $question->explanation_image_name }}</option>
                                            @endif
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
        // 必要なグローバル変数を宣言
        let questionCount = {{ count($quiz->questions) }};
        let existingQuizzes = []; // 既存のクイズを保持する配列
        let mediaFiles = { videos: [], images: [] };
        let mediaType = '';
        let mediaItems = [];
        let modal = null;
        
        // メディアファイルリストを手動で更新する関数
        async function refreshMediaFiles() {
            console.log('メディアファイルリストの手動更新を開始');
            
            const button = document.querySelector('#media-notification button');
            if (button) {
                // ボタンを無効化して更新中表示
                const originalText = button.textContent;
                button.disabled = true;
                button.textContent = '更新中...';
                
                try {
                    // メディアファイルを取得して更新
                    await fetchMediaFiles();
                    
                    // 更新が成功した場合の通知
                    const mediaNotification = document.getElementById('media-notification');
                    if (mediaFiles.videos.length > 0 || mediaFiles.images.length > 0) {
                        // メディアが存在する場合は通知を非表示
                        if (mediaNotification) {
                            mediaNotification.classList.add('hidden');
                        }
                        
                        // 一時的な成功メッセージを表示
                        const successNotice = document.createElement('div');
                        successNotice.className = 'mb-4 p-4 border-l-4 border-green-500 bg-green-50 dark:bg-green-900/30 text-green-800 dark:text-green-200';
                        successNotice.innerHTML = `
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-green-600 dark:text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm mb-1">メディアファイルリストが更新されました。</p>
                                    <p class="text-sm mt-1">動画: ${mediaFiles.videos.length}件, 画像: ${mediaFiles.images.length}件</p>
                                </div>
                            </div>
                        `;
                        
                        mediaNotification.parentNode.insertBefore(successNotice, mediaNotification.nextSibling);
                        
                        // 3秒後に成功メッセージを消す
                        setTimeout(() => {
                            successNotice.remove();
                        }, 3000);
                    } else {
                        // メディアが存在しない場合
                        if (mediaNotification) {
                            mediaNotification.classList.remove('hidden');
                        }
                    }
                    
                    // ドロップダウンの更新を強制実行
                    updateMediaSelects();
                } catch (error) {
                    console.error('メディアファイルリストの更新に失敗しました:', error);
                    
                    // エラーメッセージを表示
                    const errorNotice = document.createElement('div');
                    errorNotice.className = 'mb-4 p-4 border-l-4 border-red-500 bg-red-50 dark:bg-red-900/30 text-red-800 dark:text-red-200';
                    errorNotice.innerHTML = `
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-600 dark:text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm"><strong>エラー:</strong> メディアファイルリストの更新に失敗しました。</p>
                            </div>
                        </div>
                    `;
                    
                    const mediaNotification = document.getElementById('media-notification');
                    if (mediaNotification) {
                        mediaNotification.parentNode.insertBefore(errorNotice, mediaNotification.nextSibling);
                    }
                    
                    // 3秒後にエラーメッセージを消す
                    setTimeout(() => {
                        errorNotice.remove();
                    }, 3000);
                } finally {
                    // ボタンを元に戻す
                    if (button) {
                        button.disabled = false;
                        button.textContent = originalText;
                    }
                }
            }
        }
        
        // 既存のクイズを取得
        async function fetchExistingQuizzes() {
            try {
                // APIエンドポイントからクイズデータを取得
                const response = await fetch('{{ url('/api/quizzes') }}');
                const data = await response.json();
                existingQuizzes = data;
                return existingQuizzes;
            } catch (error) {
                console.error('クイズの取得に失敗しました:', error);
                return [];
            }
        }

        // メディアファイルを取得 - Create画面と同様のシンプルな実装
        async function fetchMediaFiles() {
            try {
                const response = await fetch('{{ route('media.index') }}');
                
                // レスポンスステータスをチェック
                if (!response.ok) {
                    throw new Error(`サーバーエラー：HTTPステータス ${response.status}`);
                }
                
                // レスポンスのContent-Typeをチェック
                const contentType = response.headers.get("content-type");
                if (contentType && contentType.indexOf("application/json") === -1) {
                    console.warn("JSONではないレスポンスを受信しました。");
                    mediaFiles = { videos: [], images: [] };
                    return mediaFiles;
                }
                
                const data = await response.json();
                
                // データの形式を確認
                if (data && typeof data === 'object') {
                    mediaFiles = {
                        videos: Array.isArray(data.videos) ? data.videos : [],
                        images: Array.isArray(data.images) ? data.images : []
                    };
                    
                    console.log('メディアファイル取得成功 - 動画:', mediaFiles.videos.length, '画像:', mediaFiles.images.length);
                    return mediaFiles;
                } else {
                    console.warn('無効なデータ形式を受信しました');
                    mediaFiles = { videos: [], images: [] };
                    return mediaFiles;
                }
            } catch (error) {
                console.error('メディアファイルの取得に失敗しました:', error);
                mediaFiles = { videos: [], images: [] };
                return mediaFiles;
            }
        }

        // 次のクイズ選択肢を更新
        function updateNextQuizSelects() {
            const selects = document.querySelectorAll('select[name$="[next_quiz_id]"]');
            selects.forEach(select => {
                const currentValue = select.value;
                select.innerHTML = '<option value="">次のクイズなし</option>';
                
                // 現在編集中のクイズ以外の選択肢を表示
                const currentEditingQuizId = {{ $quiz->id }};
                existingQuizzes.forEach(quiz => {
                    if (quiz.id !== currentEditingQuizId) {
                        const option = document.createElement('option');
                        option.value = quiz.id;
                        option.textContent = quiz.title;
                        select.appendChild(option);
                    }
                });
                
                if (currentValue) {
                    // もし値がoption内に存在するなら選択
                    const optionExists = Array.from(select.options).some(option => option.value === currentValue);
                    if (optionExists) {
                        select.value = currentValue;
                    }
                }
            });
        }

        // メディア選択肢を更新
        function updateMediaSelects() {
            console.log('メディア選択肢の更新を開始');
            
            // 選択肢を更新する対象を取得
            const videoSelects = document.querySelectorAll('select[name$="[media_name]"]');
            const imageSelects = document.querySelectorAll('select[name$="[explanation_image_name]"]');
            
            console.log('更新対象 - 動画セレクト:', videoSelects.length, '画像セレクト:', imageSelects.length);

            // 動画選択肢の更新
            videoSelects.forEach(select => {
                const currentValue = select.value;
                // いったんクリア
                select.innerHTML = '<option value="">なし</option>';
                
                // 選択肢を追加
                if (mediaFiles.videos && mediaFiles.videos.length) {
                    mediaFiles.videos.forEach(video => {
                        const option = document.createElement('option');
                        option.value = video.filename;
                        option.textContent = video.title || video.filename;
                        select.appendChild(option);
                    });
                }
                
                // 現在の値を復元
                if (currentValue) {
                    // 値が存在するかチェック
                    const exists = Array.from(select.options).some(option => option.value === currentValue);
                    if (exists) {
                        select.value = currentValue;
                    } else if (currentValue !== '') {
                        // 存在しないけど値がある場合、削除されたファイル用のオプションを追加
                        const option = document.createElement('option');
                        option.value = currentValue;
                        option.textContent = currentValue + ' (削除済み)';
                        select.appendChild(option);
                        select.value = currentValue;
                    }
                }
            });

            // 画像選択肢の更新
            imageSelects.forEach(select => {
                const currentValue = select.value;
                // いったんクリア
                select.innerHTML = '<option value="">なし</option>';
                
                // 選択肢を追加
                if (mediaFiles.images && mediaFiles.images.length) {
                    mediaFiles.images.forEach(image => {
                        const option = document.createElement('option');
                        option.value = image.filename;
                        option.textContent = image.title || image.filename;
                        select.appendChild(option);
                    });
                }
                
                // 現在の値を復元
                if (currentValue) {
                    // 値が存在するかチェック
                    const exists = Array.from(select.options).some(option => option.value === currentValue);
                    if (exists) {
                        select.value = currentValue;
                    } else if (currentValue !== '') {
                        // 存在しないけど値がある場合、削除されたファイル用のオプションを追加
                        const option = document.createElement('option');
                        option.value = currentValue;
                        option.textContent = currentValue + ' (削除済み)';
                        select.appendChild(option);
                        select.value = currentValue;
                    }
                }
            });
            
            console.log('メディア選択肢の更新完了');
            
            // メディアが存在しない場合はメッセージを表示
            const mediaNotification = document.getElementById('media-notification');
            if (mediaNotification) {
                if ((!mediaFiles.videos || mediaFiles.videos.length === 0) && 
                    (!mediaFiles.images || mediaFiles.images.length === 0)) {
                    mediaNotification.classList.remove('hidden');
                } else {
                    mediaNotification.classList.add('hidden');
                }
            }
        }

        // DOM 読み込み完了時の処理をシンプルに
        document.addEventListener('DOMContentLoaded', async function() {
            console.log('DOM初期化: 処理を開始します');
            console.log('%c🔍 デバッグヘルプ', 'font-size:14px; font-weight:bold; color:#4CAF50;');
            console.log('%cメディアドロップダウンに問題がある場合は、次の内容を確認してください:', 'font-weight:bold;');
            console.log('1. APIレスポンス: media.indexからのレスポンスデータ');
            console.log('2. メディアファイル数: 動画と画像の数が0でないこと');
            console.log('3. updateMediaSelects関数: ドロップダウンが正しく更新されているか');
            console.log('4. CORSエラー: ブラウザのコンソールにCORSエラーがないか');
            console.log('5. キャッシュ問題: Ctrl+F5でハードリロードを試してみる');
            
            // メディア通知エリアのボタンにリスナーを設定
            const refreshButton = document.querySelector('#media-notification button');
            if (refreshButton) {
                refreshButton.addEventListener('click', refreshMediaFiles);
                console.log('メディア更新ボタンにイベントリスナーを設定しました');
            }
            
            try {
                // 並行して両方のデータを取得
                const [quizzes, media] = await Promise.all([
                    fetchExistingQuizzes(),
                    fetchMediaFiles()
                ]);
                
                console.log('データ取得完了 - クイズ:', existingQuizzes.length, '件');
                console.log('データ取得完了 - メディア: 動画', mediaFiles.videos.length, '件, 画像', mediaFiles.images.length, '件');
                
                // クイズとメディア選択肢を更新
                updateNextQuizSelects();
                updateMediaSelects();
                
                // メディアが存在しない場合は通知を表示
                if ((!mediaFiles.videos || mediaFiles.videos.length === 0) && 
                    (!mediaFiles.images || mediaFiles.images.length === 0)) {
                    console.warn('メディアファイルが存在しません。メディアファイルを登録してください。');
                    const mediaNotification = document.getElementById('media-notification');
                    if (mediaNotification) {
                        mediaNotification.classList.remove('hidden');
                    }
                }
            } catch (error) {
                console.error('初期化中にエラーが発生しました:', error);
            }
        });

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
            console.log('openMediaRegistrationModal実行:', type);
            mediaType = type; // グローバル変数に保存
            console.log('mediaType設定:', mediaType);
            
            // 既存のモーダルがあれば削除
            const existingModal = document.querySelector('.media-registration-modal');
            if (existingModal) {
                console.log('既存のモーダルを削除します');
                existingModal.remove();
            }
            
            modal = document.createElement('div');
            modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 media-registration-modal';
            
            console.log('モーダルDOMを作成中');
            
            modal.innerHTML = `
                <div class="relative top-20 mx-auto p-6 border w-[600px] shadow-lg rounded-lg bg-white dark:bg-gray-800">
                    <div class="mt-3">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                ${type === 'videos' ? '動画' : '画像'}ファイル名の管理
                            </h3>
                            <button type="button" onclick="closeMediaModal()" class="text-gray-400 hover:text-gray-500">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        
                        <!-- タブナビゲーション -->
                        <div class="border-b border-gray-200 dark:border-gray-700">
                            <nav class="flex space-x-2" aria-label="Tabs">
                                <button type="button" 
                                        class="tab-button active px-4 py-2 text-sm font-medium rounded-t-md border-b-2 border-indigo-500 text-indigo-600 dark:text-indigo-400"
                                        data-tab="register">
                                    新規登録
                                </button>
                                <button type="button" 
                                        class="tab-button px-4 py-2 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300"
                                        data-tab="edit">
                                    既存ファイル編集
                                </button>
                            </nav>
                        </div>
                        
                        <!-- 登録タブコンテンツ -->
                        <div id="register-tab" class="tab-content mt-4">
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
                                    <button type="button" onclick="closeMediaModal()" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700">
                                        キャンセル
                                    </button>
                                    <button type="submit" id="uploadButton" class="px-4 py-2 text-sm font-medium text-blue-700 bg-white dark:bg-gray-800 border-2 border-blue-600 dark:border-blue-500 rounded-md disabled:opacity-50 disabled:cursor-not-allowed">
                                        登録
                                    </button>
                                </div>
                            </form>
                        </div>
                        
                        <!-- 編集タブコンテンツ -->
                        <div id="edit-tab" class="tab-content mt-4 hidden">
                            <div class="search-box mb-4">
                                <input type="text" id="media-search" placeholder="検索..." class="w-full px-3 py-2 border rounded-md text-sm">
                            </div>
                            <div id="media-list" class="border rounded-md overflow-hidden">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-800">
                                        <tr>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-8">
                                                <input type="checkbox" id="select-all-media" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                            </th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                ファイル名
                                            </th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                タイトル
                                            </th>
                                            <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                操作
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody id="media-items" class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-800">
                                        <!-- メディアアイテムがここに動的に追加されます -->
                                        <tr>
                                            <td colspan="4" class="px-4 py-4 text-sm text-center text-gray-500 dark:text-gray-400">
                                                読み込み中...
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-4 flex justify-end">
                                <button type="button" id="bulk-delete-btn" class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                                    選択したメディアを削除
                                </button>
                            </div>
                        </div>
                        
                        <!-- 編集フォームモーダル（初期状態では非表示） -->
                        <div id="edit-form-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-96 max-w-full">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">メディア情報の編集</h3>
                                <form id="mediaEditForm" class="space-y-4">
                                    <input type="hidden" name="id" id="edit-id">
                                    <div>
                                        <label for="edit-filename" class="block text-sm font-medium text-gray-700 dark:text-gray-300">ファイル名 <span class="text-red-500">*</span></label>
                                        <input type="text" name="filename" id="edit-filename" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500" required>
                                    </div>
                                    <div>
                                        <label for="edit-title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">タイトル</label>
                                        <input type="text" name="title" id="edit-title" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>
                                    <div>
                                        <label for="edit-description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">説明</label>
                                        <textarea name="description" id="edit-description" rows="2" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                                    </div>
                                    <div class="flex justify-end space-x-3">
                                        <button type="button" onclick="closeEditForm()" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700">
                                            キャンセル
                                        </button>
                                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            更新
                                        </button>
                                    </div>
                                </form>
                            </div>
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
            // const mediaType = type; ←この行を削除
            // let mediaItems = []; ←この行を削除

            // ファイル選択時にファイル名を表示
            fileInput.addEventListener('change', function() {
                if (this.files.length === 1) {
                    filenameInput.value = this.files[0].name;
                    
                    // ファイル名から自動的にタイトルを生成（拡張子を除去）
                    const titleWithoutExtension = this.files[0].name.split('.').slice(0, -1).join('.');
                    const autoTitle = titleWithoutExtension
                        .replace(/[-_]/g, ' ')  // ハイフンとアンダースコアをスペースに置換
                        .replace(/\b\w/g, l => l.toUpperCase());  // 単語の先頭を大文字に
                    
                    titleInput.value = autoTitle;
                } else if (this.files.length > 1) {
                    filenameInput.value = `${this.files.length}個のファイルが選択されています`;
                    titleInput.value = '';  // 複数選択時はタイトルをクリア
                } else {
                    filenameInput.value = '';
                    titleInput.value = '';
                }
            });
            
            // メディアアップロードフォームの送信
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                // フォームデータ取得
                const formData = new FormData(this);
                const files = fileInput.files;
                
                if (!files || files.length === 0) {
                    alert('ファイルを選択してください。');
                    return;
                }
                
                // 登録ボタンを無効化して処理中表示
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
                                .replace(/[-_]/g, ' ')
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
                            showNotification(`${successCount}個のファイルが正常に登録されました。${errorMessages.length > 0 ? `${errorMessages.length}個は失敗しました。` : ''}`, 'success');
                            
                            // エラーがあれば詳細を表示
                            if (errorMessages.length > 0) {
                                const errorHTML = errorMessages.map(msg => `<li>${msg}</li>`).join('');
                                showNotification(`登録エラー詳細: <ul class="mt-1 list-disc list-inside text-xs">${errorHTML}</ul>`, 'error', 8000);
                            }
                        } else if (errorMessages.length > 0) {
                            showNotification('すべてのファイルの登録に失敗しました。', 'error');
                            const errorHTML = errorMessages.map(msg => `<li>${msg}</li>`).join('');
                            showNotification(`エラー詳細: <ul class="mt-1 list-disc list-inside text-xs">${errorHTML}</ul>`, 'error', 8000);
                        }
                    } else {
                        // 単一ファイルの場合
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
                        
                        const data = await response.json();
                        
                        if (response.ok) {
                            // 成功時、メディアファイルリストを更新
                            await fetchMediaFiles();
                            
                            // モーダルを閉じる
                            modal.remove();
                            
                            // 成功メッセージ表示
                            showNotification('ファイルが正常に登録されました。', 'success');
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
            
            // タブ切り替え機能
            const tabButtons = modal.querySelectorAll('.tab-button');
            const tabContents = modal.querySelectorAll('.tab-content');
            
            console.log('タブボタン数:', tabButtons.length);
            console.log('タブコンテンツ数:', tabContents.length);
            
            tabButtons.forEach((button, index) => {
                console.log(`タブボタン[${index}]:`, button.getAttribute('data-tab'));
                button.addEventListener('click', (e) => {
                    console.log('タブクリック:', button.getAttribute('data-tab'));
                    
                    // すべてのタブをinactive状態にする
                    tabButtons.forEach(btn => {
                        btn.classList.remove('active', 'border-b-2', 'border-indigo-500', 'text-indigo-600', 'dark:text-indigo-400');
                        btn.classList.add('text-gray-500', 'dark:text-gray-400', 'hover:text-gray-700', 'dark:hover:text-gray-300');
                    });
                    
                    // クリックされたタブをactive状態にする
                    button.classList.add('active', 'border-b-2', 'border-indigo-500', 'text-indigo-600', 'dark:text-indigo-400');
                    button.classList.remove('text-gray-500', 'dark:text-gray-400', 'hover:text-gray-700', 'dark:hover:text-gray-300');
                    
                    // すべてのコンテンツを非表示にする
                    tabContents.forEach(content => {
                        content.classList.add('hidden');
                    });
                    
                    // 対応するコンテンツを表示する
                    const tabName = button.getAttribute('data-tab');
                    const tabContent = modal.querySelector(`#${tabName}-tab`);
                    console.log(`タブコンテンツ[${tabName}]:`, tabContent);
                    
                    if (tabContent) {
                        tabContent.classList.remove('hidden');
                        console.log(`${tabName}-tabの表示状態:`, !tabContent.classList.contains('hidden'));
                    } else {
                        console.error(`#${tabName}-tabが見つかりません`);
                    }
                    
                    // 編集タブが選択された場合、メディアリストを読み込む
                    if (tabName === 'edit') {
                        console.log('編集タブが選択されました。メディアリスト読み込み開始');
                        loadMediaItems();
                    }
                });
            });
            
            // メディアアイテムの読み込み
            async function loadMediaItems() {
                console.log('loadMediaItems関数実行開始');
                const mediaList = modal.querySelector('#media-items');
                console.log('mediaList要素:', mediaList);
                
                if (!mediaList) {
                    console.error('#media-items要素が見つかりません');
                    return;
                }
                
                mediaList.innerHTML = '<tr><td colspan="4" class="px-4 py-4 text-sm text-center text-gray-500 dark:text-gray-400">読み込み中...</td></tr>';
                
                try {
                    console.log('fetch実行: {{ route('media.index') }}');
                    const response = await fetch('{{ route('media.index') }}', {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    console.log('レスポンス取得:', response.status, response.statusText);
                    
                    if (!response.ok) {
                        console.error('メディア取得APIエラー:', response.status, response.statusText);
                        mediaList.innerHTML = `<tr><td colspan="4" class="px-4 py-4 text-sm text-center text-red-500">エラー: APIステータス ${response.status}</td></tr>`;
                        return;
                    }
                    
                    // レスポンスのContent-Typeをチェック
                    const contentType = response.headers.get("content-type");
                    console.log('レスポンスのContent-Type:', contentType);
                    
                    if (contentType && contentType.indexOf("application/json") === -1) {
                        console.warn("JSONではないレスポンスを受信しました。エラー回復を試みます。");
                        mediaList.innerHTML = `<tr><td colspan="4" class="px-4 py-4 text-sm text-center text-red-500">エラー: 無効なレスポンス形式 (${contentType})</td></tr>`;
                        return;
                    }
                    
                    let data;
                    try {
                        data = await response.json();
                        console.log('APIレスポンスデータ:', data);
                    } catch (jsonError) {
                        console.error('JSONパースエラー:', jsonError);
                        mediaList.innerHTML = `<tr><td colspan="4" class="px-4 py-4 text-sm text-center text-red-500">エラー: JSONデータの解析に失敗しました</td></tr>`;
                        return;
                    }
                    
                    // 現在のタイプ（videos/images）に一致するメディアのみをフィルタリング
                    const filterType = mediaType === 'videos' ? 'videos' : 'images';
                    console.log('フィルタータイプ:', filterType, '; 元データ:', data[filterType]);
                    
                    mediaItems = data[filterType] || [];
                    console.log('フィルタリング後のメディアアイテム数:', mediaItems.length);
                    
                    renderMediaItems(mediaItems);
                    
                    // 検索機能の設定
                    const searchInput = modal.querySelector('#media-search');
                    if (searchInput) {
                        searchInput.addEventListener('input', (e) => {
                            const searchTerm = e.target.value.toLowerCase();
                            console.log('検索キーワード:', searchTerm);
                            
                            const filteredItems = mediaItems.filter(item => 
                                item.filename.toLowerCase().includes(searchTerm) || 
                                (item.title && item.title.toLowerCase().includes(searchTerm))
                            );
                            console.log('検索結果:', filteredItems.length);
                            
                            renderMediaItems(filteredItems);
                        });
                    } else {
                        console.error('#media-search要素が見つかりません');
                    }
                    
                } catch (error) {
                    console.error('メディアリストの取得中にエラーが発生しました:', error);
                    mediaList.innerHTML = `<tr><td colspan="4" class="px-4 py-4 text-sm text-center text-red-500">エラー: ${error.message}</td></tr>`;
                }
            }
            
            // メディアアイテムのレンダリング
            function renderMediaItems(items) {
                console.log('renderMediaItems関数実行 - アイテム数:', items.length);
                const mediaList = modal.querySelector('#media-items');
                
                if (!mediaList) {
                    console.error('renderMediaItems: #media-items要素が見つかりません');
                    return;
                }
                
                if (items.length === 0) {
                    console.log('メディアアイテムが0件のため、該当なしメッセージを表示');
                    mediaList.innerHTML = '<tr><td colspan="4" class="px-4 py-4 text-sm text-center text-gray-500 dark:text-gray-400">メディアが見つかりません</td></tr>';
                    return;
                }
                
                console.log('メディアリストのレンダリング開始');
                mediaList.innerHTML = '';
                
                items.forEach((item, index) => {
                    console.log(`メディアアイテム[${index}]:`, item.id, item.filename);
                    const row = document.createElement('tr');
                    row.className = 'hover:bg-gray-50 dark:hover:bg-gray-800';
                    
                    row.innerHTML = `
                        <td class="px-4 py-3 text-sm">
                            <input type="checkbox" data-id="${item.id}" class="media-checkbox h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100 font-mono">
                            ${item.filename}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                            ${item.title || ''}
                        </td>
                        <td class="px-4 py-3 text-sm text-right">
                            <div class="flex justify-end space-x-2">
                                <button type="button" data-id="${item.id}" class="edit-media-btn px-3 py-1 text-sm font-medium text-indigo-600 bg-indigo-100 hover:bg-indigo-200 rounded-md dark:text-indigo-400 dark:bg-indigo-900/30 dark:hover:bg-indigo-800/40">
                                    編集
                                </button>
                                <button type="button" data-id="${item.id}" data-filename="${item.filename}" class="delete-media-btn px-3 py-1 text-sm font-medium text-red-600 bg-red-100 hover:bg-red-200 rounded-md dark:text-red-400 dark:bg-red-900/30 dark:hover:bg-red-800/40">
                                    削除
                                </button>
                            </div>
                        </td>
                    `;
                    
                    mediaList.appendChild(row);
                });
                
                console.log('メディアリストのレンダリング完了');
                
                // 編集ボタンのイベントハンドラを設定
                const editBtns = modal.querySelectorAll('.edit-media-btn');
                console.log('編集ボタン数:', editBtns.length);
                editBtns.forEach(button => {
                    button.addEventListener('click', () => {
                        const mediaId = button.getAttribute('data-id');
                        console.log('編集ボタンクリック:', mediaId);
                        const mediaItem = mediaItems.find(item => item.id == mediaId);
                        
                        if (mediaItem) {
                            openEditForm(mediaItem);
                        } else {
                            console.error('編集対象のメディアアイテムが見つかりません:', mediaId);
                        }
                    });
                });
                
                // 削除ボタンのイベントハンドラを設定
                modal.querySelectorAll('.delete-media-btn').forEach(button => {
                    button.addEventListener('click', async () => {
                        const mediaId = button.getAttribute('data-id');
                        const filename = button.getAttribute('data-filename');
                        
                        if (confirm(`「${filename}」を削除してもよろしいですか？\n\nこの操作は元に戻せません。`)) {
                            await deleteMedia(mediaId);
                        }
                    });
                });

                // チェックボックスの状態変更時に一括削除ボタンの状態を更新
                const checkboxes = modal.querySelectorAll('.media-checkbox');
                const bulkDeleteBtn = modal.querySelector('#bulk-delete-btn');
                
                checkboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', updateBulkDeleteButton);
                });

                // 全選択チェックボックスのイベントハンドラを設定
                const selectAllCheckbox = modal.querySelector('#select-all-media');
                selectAllCheckbox.checked = false;
                selectAllCheckbox.addEventListener('change', function() {
                    checkboxes.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                    updateBulkDeleteButton();
                });

                // 一括削除ボタンのイベントハンドラを設定
                bulkDeleteBtn.addEventListener('click', bulkDeleteMedia);

                // 一括削除ボタンの状態更新
                function updateBulkDeleteButton() {
                    const checkedCount = modal.querySelectorAll('.media-checkbox:checked').length;
                    bulkDeleteBtn.disabled = checkedCount === 0;
                    bulkDeleteBtn.textContent = checkedCount > 0 
                        ? `選択したメディアを削除 (${checkedCount})` 
                        : '選択したメディアを削除';
                }

                // 一括削除処理
                async function bulkDeleteMedia() {
                    const selectedIds = Array.from(modal.querySelectorAll('.media-checkbox:checked'))
                        .map(checkbox => checkbox.getAttribute('data-id'));
                    
                    if (selectedIds.length === 0) return;
                    
                    if (confirm(`選択した${selectedIds.length}件のメディアを削除してもよろしいですか？\n\nこの操作は元に戻せません。`)) {
                        let successCount = 0;
                        let errorMessages = [];
                        
                        // プログレスバーを表示
                        const progressContainer = document.createElement('div');
                        progressContainer.className = 'mt-4';
                        progressContainer.innerHTML = `
                            <p class="text-sm mb-1">削除中... <span id="delete-progress-text">0/${selectedIds.length}</span></p>
                            <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                                <div id="delete-progress-bar" class="bg-red-600 h-2.5 rounded-full" style="width: 0%"></div>
                            </div>
                        `;
                        
                        const mediaListContainer = modal.querySelector('#media-list');
                        mediaListContainer.after(progressContainer);
                        
                        const progressBar = progressContainer.querySelector('#delete-progress-bar');
                        const progressText = progressContainer.querySelector('#delete-progress-text');
                        
                        // 削除ボタンを無効化
                        bulkDeleteBtn.disabled = true;
                        bulkDeleteBtn.innerHTML = `
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            削除中...
                        `;
                        
                        // 順番に削除処理を実行
                        for (let i = 0; i < selectedIds.length; i++) {
                            const mediaId = selectedIds[i];
                            
                            try {
                                const response = await fetch('{{ route('media.destroy') }}', {
                                    method: 'DELETE',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json'
                                    },
                                    body: JSON.stringify({ id: mediaId })
                                });
                                
                                if (response.ok) {
                                    successCount++;
                                } else {
                                    const errorData = await response.json();
                                    const mediaFilename = mediaItems.find(item => item.id == mediaId)?.filename || 'ID: ' + mediaId;
                                    errorMessages.push(`${mediaFilename}: ${errorData.message || '削除に失敗しました'}`);
                                }
                            } catch (error) {
                                const mediaFilename = mediaItems.find(item => item.id == mediaId)?.filename || 'ID: ' + mediaId;
                                errorMessages.push(`${mediaFilename}: ${error.message}`);
                            }
                            
                            // プログレスバーを更新
                            const progress = Math.round(((i + 1) / selectedIds.length) * 100);
                            progressBar.style.width = `${progress}%`;
                            progressText.textContent = `${i + 1}/${selectedIds.length}`;
                        }
                        
                        // プログレスバーを削除
                        setTimeout(() => {
                            progressContainer.remove();
                        }, 1000);
                        
                        // メディアリストを再読み込み
                        await loadMediaItems();
                        
                        // メディア選択肢も更新
                        await fetchMediaFiles();
                        
                        // 操作結果を通知
                        if (successCount > 0) {
                            if (errorMessages.length > 0) {
                                showNotification(`${successCount}件のメディアを削除しました。${errorMessages.length}件は失敗しました。`, 'success');
                                
                                // エラー詳細を表示
                                console.error('削除エラー:', errorMessages);
                                const errorHTML = errorMessages.map(msg => `<li>${msg}</li>`).join('');
                                showNotification(`削除エラー詳細: <ul class="mt-1 list-disc list-inside text-xs">${errorHTML}</ul>`, 'error', 8000);
                            } else {
                                showNotification(`${successCount}件のメディアを削除しました。`, 'success');
                            }
                        } else if (errorMessages.length > 0) {
                            showNotification('すべてのメディアの削除に失敗しました。', 'error');
                        }
                        
                        // 削除ボタンをリセット
                        bulkDeleteBtn.innerHTML = '選択したメディアを削除';
                        bulkDeleteBtn.disabled = true;
                    }
                }
            }
            
            // 編集フォームを開く
            async function openEditForm(mediaItem) {
                try {
                    // 詳細情報を取得（説明文などの追加情報が必要な場合）
                    // この例では簡略化のため、mediaItemに既に必要な情報があると仮定
                    
                    const editForm = modal.querySelector('#edit-form-modal');
                    const idInput = modal.querySelector('#edit-id');
                    const filenameInput = modal.querySelector('#edit-filename');
                    const titleInput = modal.querySelector('#edit-title');
                    const descriptionInput = modal.querySelector('#edit-description');
                    
                    idInput.value = mediaItem.id;
                    filenameInput.value = mediaItem.filename;
                    titleInput.value = mediaItem.title || '';
                    
                    // 説明文はAPIレスポンスに含まれていない可能性があるため、別途取得が必要かもしれない
                    // ここでは簡略化のため空文字を設定
                    descriptionInput.value = mediaItem.description || '';
                    
                    editForm.classList.remove('hidden');
                } catch (error) {
                    console.error('メディア情報の取得に失敗しました:', error);
                    alert('メディア情報の取得に失敗しました。再度お試しください。');
                }
            }
            
            // 編集フォームを閉じる
            function closeEditForm() {
                console.log('closeEditForm実行');
                const editForm = modal.querySelector('#edit-form-modal');
                if (editForm) {
                    editForm.classList.add('hidden');
                } else {
                    console.error('#edit-form-modalが見つかりません');
                }
            }
            
            // メディア削除処理
            async function deleteMedia(mediaId) {
                try {
                    const response = await fetch('{{ route('media.destroy') }}', {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ id: mediaId })
                    });
                    
                    if (response.ok) {
                        // 成功時、メディアリストを再読み込み
                        await loadMediaItems();
                        // メディア選択肢も更新
                        await fetchMediaFiles();
                        
                        showNotification('メディアが削除されました。', 'success');
                    } else {
                        const errorData = await response.json();
                        throw new Error(errorData.message || 'メディアの削除に失敗しました。');
                    }
                } catch (error) {
                    console.error('メディア削除エラー:', error);
                    showNotification(`削除に失敗しました: ${error.message}`, 'error');
                }
            }
            
            // 通知メッセージを表示
            function showNotification(message, type = 'info', duration = 3000) {
                const notification = document.createElement('div');
                notification.className = `fixed bottom-4 right-4 px-4 py-3 rounded-lg shadow-lg z-50 ${
                    type === 'success' ? 'bg-green-100 border-l-4 border-green-500 text-green-700' : 
                    type === 'error' ? 'bg-red-100 border-l-4 border-red-500 text-red-700' : 
                    'bg-blue-100 border-l-4 border-blue-500 text-blue-700'
                }`;
                
                notification.innerHTML = `
                    <div class="flex items-start">
                        <div class="py-1 mr-3 flex-shrink-0">
                            ${type === 'success' ? 
                                '<svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>' : 
                                type === 'error' ? 
                                '<svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>' : 
                                '<svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
                            }
                        </div>
                        <div class="max-w-xs">
                            ${message}
                        </div>
                    </div>
                `;
                
                document.body.appendChild(notification);
                
                // 指定時間後に通知を消す
                setTimeout(() => {
                    notification.remove();
                }, duration);
            }
            
            // 編集フォームの送信ハンドラ
            const editForm = modal.querySelector('#mediaEditForm');
            editForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                const mediaId = formData.get('id');
                const filename = formData.get('filename');
                const title = formData.get('title');
                const description = formData.get('description');
                
                try {
                    const response = await fetch('{{ route('media.update') }}', {
                        method: 'PUT',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            id: mediaId,
                            filename: filename,
                            title: title,
                            description: description
                        })
                    });
                    
                    if (response.ok) {
                        const result = await response.json();
                        
                        // 編集フォームを閉じる
                        closeEditForm();
                        
                        // メディアリストを再読み込み
                        await loadMediaItems();
                        
                        // メディア選択肢も更新
                        await fetchMediaFiles();
                        
                        showNotification('メディア情報が更新されました。', 'success');
                    } else {
                        const errorData = await response.json();
                        throw new Error(errorData.message || 'メディア情報の更新に失敗しました。');
                    }
                } catch (error) {
                    console.error('メディア更新エラー:', error);
                    showNotification(`更新に失敗しました: ${error.message}`, 'error');
                }
            });
            
            // 編集フォームを閉じる関数をグローバルに定義
            window.closeEditForm = function() {
                console.log('closeEditForm実行');
                const editFormModal = document.querySelector('#edit-form-modal');
                if (editFormModal) {
                    editFormModal.classList.add('hidden');
                } else {
                    console.error('#edit-form-modalが見つかりません');
                }
            };
            
            // モーダルを閉じる関数をグローバルに定義
            window.closeMediaModal = function() {
                console.log('closeMediaModal実行');
                const modalElement = document.querySelector('.media-registration-modal');
                if (modalElement) {
                    modalElement.remove();
                } else {
                    console.error('.media-registration-modalが見つかりません');
                }
            };
        }

        // フォーム送信のハンドリング
        document.getElementById('quizForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const form = e.target;
            const errorDiv = document.getElementById('error-messages');
            errorDiv.innerHTML = ''; // エラーメッセージをクリア

            try {
                const formData = new FormData(form);
                
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                // ステータスコードとContentTypeをログ出力
                console.log('クイズ更新レスポンス:', response.status, response.statusText);
                console.log('Content-Type:', response.headers.get('content-type'));

                let result;
                try {
                    result = await response.json();
                } catch (jsonError) {
                    console.error('JSONパースエラー:', jsonError);
                    errorDiv.innerHTML = '<p>レスポンスの解析に失敗しました。サーバーの応答が不正です。</p>';
                    errorDiv.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    return;
                }

                if (!response.ok) {
                    // バリデーションエラーの処理
                    if (response.status === 422 && result.errors) {
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
                        throw new Error(result.message || '送信に失敗しました');
                    }
                } else {
                    // 成功時の処理
                    window.location.href = result.redirect || '/quizzes';
                }
            } catch (error) {
                console.error('フォーム送信エラー:', error);
                errorDiv.innerHTML = `<p>${error.message}</p>`;
                errorDiv.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    </script>
    @endpush
</x-app-layout> 