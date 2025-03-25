<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('成績詳細') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4">
                <a href="{{ route('player-records.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    成績一覧に戻る
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <!-- 成績概要 -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div>
                            <h3 class="text-lg font-medium mb-4">基本情報</h3>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <dl class="grid grid-cols-1 gap-3">
                                    <div class="flex flex-col">
                                        <dt class="text-sm font-medium text-gray-500">ユーザー</dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            <a href="{{ route('player-records.user', $record->user) }}" class="text-indigo-600 hover:text-indigo-900">
                                                {{ $record->user->name }}
                                            </a>
                                        </dd>
                                    </div>
                                    <div class="flex flex-col">
                                        <dt class="text-sm font-medium text-gray-500">クイズ</dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            <a href="{{ route('player-records.quiz', $record->quiz) }}" class="text-indigo-600 hover:text-indigo-900">
                                                {{ $record->quiz->title }}
                                            </a>
                                        </dd>
                                    </div>
                                    <div class="flex flex-col">
                                        <dt class="text-sm font-medium text-gray-500">完了日時</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $record->completed_at->format('Y/m/d H:i:s') }}</dd>
                                    </div>
                                    <div class="flex flex-col">
                                        <dt class="text-sm font-medium text-gray-500">VRセッションID</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $record->vr_session_id ?? '情報なし' }}</dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-medium mb-4">成績サマリー</h3>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <dl class="grid grid-cols-1 gap-3">
                                    <div class="flex flex-col">
                                        <dt class="text-sm font-medium text-gray-500">合計スコア</dt>
                                        <dd class="mt-1 text-lg font-bold text-blue-600">{{ $record->total_score }}点</dd>
                                    </div>
                                    <div class="flex flex-col">
                                        <dt class="text-sm font-medium text-gray-500">所要時間</dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            @if($record->time_taken)
                                                {{ floor($record->time_taken / 60) }}分{{ $record->time_taken % 60 }}秒
                                            @else
                                                情報なし
                                            @endif
                                        </dd>
                                    </div>
                                    <div class="flex flex-col">
                                        <dt class="text-sm font-medium text-gray-500">正解数 / 問題数</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $correctAnswers }} / {{ $totalQuestions }}</dd>
                                    </div>
                                    <div class="flex flex-col">
                                        <dt class="text-sm font-medium text-gray-500">正答率</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ number_format($correctRate, 1) }}%</dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                    </div>

                    <!-- 質問ごとの成績 -->
                    <div>
                        <h3 class="text-lg font-medium mb-4">質問ごとの成績</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">質問</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">選択した回答</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">正誤</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">所要時間</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">獲得ポイント</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($record->questionRecords as $questionRecord)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $questionRecord->question->question_text }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $questionRecord->option ? $questionRecord->option->option_text : '未回答' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($questionRecord->is_correct)
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        正解
                                                    </span>
                                                @else
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                        不正解
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                @if($questionRecord->time_taken)
                                                    {{ $questionRecord->time_taken }}秒
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $questionRecord->points_earned }}点
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 