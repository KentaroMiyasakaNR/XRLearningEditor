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
            modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50';
            modal.innerHTML = `
                <div class="relative top-20 mx-auto p-6 border w-96 shadow-lg rounded-lg bg-white dark:bg-gray-800">
                    <div class="mt-3">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                ${type === 'videos' ? '動画' : '画像'}ファイルのアップロード
                            </h3>
                            <button type="button" onclick="this.closest('.fixed').remove()" class="text-gray-400 hover:text-gray-500">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        <div class="mt-2">
                            <form id="mediaUploadForm" class="space-y-4">
                                <div class="border-2 border-dashed border-gray-300 dark:border-gray-700 rounded-lg p-6 text-center cursor-pointer bg-gray-50 dark:bg-gray-900/50 hover:bg-gray-100 dark:hover:bg-gray-900 transition-all duration-200" id="dropzone">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                        ${type === 'videos' ? '動画ファイル' : '画像ファイル'}をドラッグ＆ドロップ
                                    </p>
                                    ${type === 'videos' ? `<p class="text-xs text-gray-500 dark:text-gray-500">対応形式: MP4, MOV, AVI, WebM, Ogg などの動画形式</p>` : ''}
                                    <p class="text-xs text-gray-500 dark:text-gray-500 mt-1 mb-3">
                                        または
                                    </p>
                                    <input type="file" name="file" id="fileInput" class="hidden" ${type === 'videos' ? 'accept="video/*"' : 'accept="image/*"'} required>
                                    <button type="button" id="browseButton" class="px-6 py-2.5 text-sm font-medium rounded-md shadow-md bg-white text-blue-700 border-2 border-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        <svg class="w-5 h-5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                        </svg>
                                        ファイルを選択
                                    </button>
                                    <div id="selected-file" class="mt-3 text-sm text-gray-600 dark:text-gray-400 hidden">
                                        選択済み: <span id="filename" class="font-medium"></span>
                                    </div>
                                </div>
                                <input type="hidden" name="type" value="${type}">
                                <div class="flex justify-end space-x-3 mt-2">
                                    <button type="button" onclick="this.closest('.fixed').remove()" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700">
                                        キャンセル
                                    </button>
                                    <button type="submit" id="uploadButton" class="px-4 py-2 text-sm font-medium text-blue-700 bg-white dark:bg-gray-800 border-2 border-blue-600 dark:border-blue-500 rounded-md disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                                        アップロード
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);

            // ドラッグ＆ドロップ機能
            const dropzone = modal.querySelector('#dropzone');
            const fileInput = modal.querySelector('#fileInput');
            const browseButton = modal.querySelector('#browseButton');
            const selectedFileDiv = modal.querySelector('#selected-file');
            const filename = modal.querySelector('#filename');
            const uploadButton = modal.querySelector('#uploadButton');

            browseButton.addEventListener('click', () => {
                fileInput.click();
            });

            fileInput.addEventListener('change', () => {
                if (fileInput.files.length > 0) {
                    filename.textContent = fileInput.files[0].name;
                    selectedFileDiv.classList.remove('hidden');
                    uploadButton.disabled = false;
                }
            });

            // ドラッグ&ドロップイベント
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropzone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                }, false);
            });

            ['dragenter', 'dragover'].forEach(eventName => {
                dropzone.addEventListener(eventName, () => {
                    dropzone.classList.add('border-blue-500');
                    dropzone.classList.add('bg-blue-50');
                    dropzone.classList.add('dark:bg-gray-800');
                }, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropzone.addEventListener(eventName, () => {
                    dropzone.classList.remove('border-blue-500');
                    dropzone.classList.remove('bg-blue-50');
                    dropzone.classList.remove('dark:bg-gray-800');
                }, false);
            });

            dropzone.addEventListener('drop', (e) => {
                const dt = e.dataTransfer;
                if (dt.files.length > 0) {
                    fileInput.files = dt.files;
                    filename.textContent = dt.files[0].name;
                    selectedFileDiv.classList.remove('hidden');
                    uploadButton.disabled = false;
                }
            }, false);

            // フォーム送信のハンドリング
            modal.querySelector('#mediaUploadForm').addEventListener('submit', async function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                uploadButton.disabled = true;
                uploadButton.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-blue-700 inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    アップロード中...
                `;
                
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
                        const data = await response.json();
                        throw new Error(data.message || 'アップロードに失敗しました');
                    }
                } catch (error) {
                    alert(error.message);
                    uploadButton.disabled = false;
                    uploadButton.textContent = 'アップロード';
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