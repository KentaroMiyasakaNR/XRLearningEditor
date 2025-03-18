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
                const response = await fetch('/api/quizzes');
                const data = await response.json();
                existingQuizzes = data;
                updateNextQuizSelects();
            } catch (error) {
                console.error('クイズの取得に失敗しました:', error);
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
                const data = await response.json();
                mediaFiles = data;
                updateMediaSelects();
            } catch (error) {
                console.error('メディアファイルの取得に失敗しました:', error);
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
                    option.textContent = video.filename;
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
                    option.textContent = image.filename;
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
            modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full';
            modal.innerHTML = `
                <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
                    <div class="mt-3">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">メディアファイルのアップロード</h3>
                        <div class="mt-2">
                            <form id="mediaUploadForm" class="space-y-4">
                                <input type="file" name="file" class="block w-full text-sm text-gray-500 dark:text-gray-400
                                    file:mr-4 file:py-2 file:px-4
                                    file:rounded-md file:border-0
                                    file:text-sm file:font-semibold
                                    file:bg-indigo-50 file:text-indigo-700
                                    hover:file:bg-indigo-100" required>
                                <input type="hidden" name="type" value="${type}">
                                <div class="flex justify-end space-x-3">
                                    <button type="button" onclick="this.closest('.fixed').remove()" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md">
                                        キャンセル
                                    </button>
                                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-md">
                                        アップロード
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);

            // フォーム送信のハンドリング
            modal.querySelector('#mediaUploadForm').addEventListener('submit', async function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                
                try {
                    const response = await fetch('/media', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    if (response.ok) {
                        await fetchMediaFiles();
                        modal.remove();
                    } else {
                        throw new Error('アップロードに失敗しました');
                    }
                } catch (error) {
                    alert(error.message);
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