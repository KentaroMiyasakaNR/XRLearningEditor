<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('新規クイズ作成') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <!-- エラーメッセージ表示エリア -->
                    <div id="error-messages" class="mb-4 text-red-600 dark:text-red-400"></div>
                    
                    <form action="{{ route('quizzes.store') }}" method="POST" id="quizForm" novalidate>
                        @csrf
                        <div class="mb-6">
                            <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">タイトル</label>
                            <input type="text" name="title" id="title" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500" required>
                        </div>
                        <div class="mb-6">
                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">説明</label>
                            <textarea name="description" id="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                        </div>

                        <div class="mb-6">
                            <label for="level" class="block text-sm font-medium text-gray-700 dark:text-gray-300">難易度</label>
                            <select name="level" id="level" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="S">S級</option>
                                <option value="A">A級</option>
                                <option value="B">B級</option>
                                <option value="C">C級</option>
                            </select>
                        </div>

                        <div id="questions-container">
                            <!-- 質問フォームがここに動的に追加されます -->
                        </div>

                        <div class="flex justify-between items-center mb-6">
                            <button type="button" onclick="addQuestionToForm()" style="background-color: #047857;" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                質問を追加
                            </button>

                            <button type="submit" style="background-color: #2563eb;" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                クイズを作成
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let questionCount = 0;
        let existingQuizzes = []; // 既存のクイズを保持する配列
        let mediaFiles = { videos: [], images: [] };

        // 既存のクイズを取得
        async function fetchExistingQuizzes() {
            try {
                // APIエンドポイントからクイズデータを取得
                const response = await fetch('/api/quizzes');
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
                const response = await fetch('/media');
                
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

        function addQuestionToForm() {
            const container = document.getElementById('questions-container');
            const questionDiv = document.createElement('div');
            questionDiv.className = 'mb-6 p-6 border rounded-lg';
            
            const currentCount = questionCount + 1;
            questionDiv.innerHTML = `
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium">質問 ${currentCount}</h3>
                    ${currentCount > 1 ? `
                    <button type="button" onclick="removeQuestion(this)" class="text-red-500 hover:text-red-700">
                        削除
                    </button>
                    ` : ''}
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
                    <div class="mt-1 flex items-center gap-4">
                        <select name="questions[${questionCount}][media_name]" class="flex-1 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">なし</option>
                            <!-- 動画ファイルがここに動的に追加されます -->
                        </select>
                        <button type="button" onclick="openMediaUploadModal('videos', this)" class="text-sm text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300">
                            アップロード
                        </button>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">解説テキスト</label>
                    <textarea name="questions[${questionCount}][explanation_text]" rows="3" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">解説画像</label>
                    <div class="mt-1 flex items-center gap-4">
                        <select name="questions[${questionCount}][explanation_image_name]" class="flex-1 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">なし</option>
                            <!-- 画像ファイルがここに動的に追加されます -->
                        </select>
                        <button type="button" onclick="openMediaUploadModal('images', this)" class="text-sm text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300">
                            アップロード
                        </button>
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
                                ${existingQuizzes.map(quiz => `<option value="${quiz.id}">${quiz.title}</option>`).join('')}
                            </select>
                        </div>
                        <div class="flex items-center gap-4">
                            <input type="text" name="questions[${questionCount}][options][1][option_text]" placeholder="選択肢2" class="flex-1 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500" required>
                            <input type="hidden" name="questions[${questionCount}][options][1][is_correct]" value="0">
                            <input type="checkbox" name="questions[${questionCount}][options][1][is_correct]" value="1" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label class="text-sm text-gray-600 dark:text-gray-400">正解</label>
                        </div>
                    </div>
                    <button type="button" onclick="addOption(this, ${questionCount})" class="mt-2 text-sm text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300">
                        + 選択肢を追加
                    </button>
                </div>
            `;
            container.appendChild(questionDiv);
            questionCount++;
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

        // メディアアップロードモーダルを開く
        function openMediaUploadModal(type, button) {
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
                                        <label for="filename" class="block text-sm font-medium text-gray-700 dark:text-gray-300">ファイル名 <span class="text-red-500">*</span></label>
                                        <input type="text" name="filename" id="filename" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500" placeholder="${type === 'videos' ? '例: sample_video.mp4' : '例: sample_image.jpg'}" required>
                                        <p class="mt-1 text-xs text-gray-500">
                                            ${type === 'videos' ? '動画ファイル名を入力してください。Unity側でこの名前が参照されます。' : '画像ファイル名を入力してください。'}
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
                    // JSONデータとして送信
                    const response = await fetch('/media', {
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
                        await fetchMediaFiles();
                        modal.remove();
                    } else {
                        // エラーメッセージをより詳細に表示
                        if (data.errors) {
                            const errorMessages = [];
                            for (const [field, messages] of Object.entries(data.errors)) {
                                errorMessages.push(...messages);
                            }
                            throw new Error(errorMessages.join('\n'));
                        } else if (data.message) {
                            throw new Error(data.message);
                        } else {
                            throw new Error('登録に失敗しました。');
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

        // 初期質問を1つ追加
        document.addEventListener('DOMContentLoaded', function() {
            fetchExistingQuizzes();
            fetchMediaFiles();
            addQuestionToForm();
        });
    </script>
    @endpush
</x-app-layout> 