<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TweetController;
use App\Http\Controllers\Api\TweetLikeController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\PlayerRecordsController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
  Route::apiResource('tweets', TweetController::class);
  Route::post('tweets/{tweet}/like', [TweetLikeController::class, 'store']);
  Route::delete('tweets/{tweet}/like', [TweetLikeController::class, 'destroy']);
  Route::apiResource('tweets.comments', CommentController::class);
});

// クイズ一覧を取得するAPI
Route::get('/quizzes', function() {
    return \App\Models\Quiz::select('id', 'title')->get();
});

// クイズとその質問の詳細データを取得するAPI
Route::get('/quizzes-with-questions', function() {
    return \App\Models\Quiz::with(['questions' => function($query) {
        $query->select(
            'id',
            'quiz_id',
            'question_text',
            'points',
            'media_name',
            'explanation_text',
            'explanation_image_name',
            'created_at',
            'updated_at',
            'next_quiz_id_correct',
            'next_quiz_id_incorrect'
        )->with(['options' => function($optionQuery) {
            $optionQuery->select(
                'id',
                'question_id',
                'option_text',
                'is_correct'
            );
        }]);
    }])->get();
});

// プレイヤーの成績管理API
Route::post('/player-records', [PlayerRecordsController::class, 'store']);
Route::get('/player-records', [PlayerRecordsController::class, 'index']);
Route::get('/player-records/{playerRecord}', [PlayerRecordsController::class, 'show']);
Route::get('/users/{user}/player-records', [PlayerRecordsController::class, 'userRecords']);
Route::get('/quizzes/{quiz}/player-records', [PlayerRecordsController::class, 'quizRecords']);

// ユーザー名を投げて、そのユーザーが作成したクイズとquestionsを全て返すAPI
Route::get('/users/{username}/quizzes', function($username) {
    $user = \App\Models\User::where('name', $username)->firstOrFail();
    
    return \App\Models\Quiz::where('user_id', $user->id)
        ->with(['questions' => function($query) {
            $query->select(
                'id',
                'quiz_id',
                'question_text',
                'points',
                'media_name',
                'explanation_text',
                'explanation_image_name',
                'created_at',
                'updated_at',
                'next_quiz_id_correct',
                'next_quiz_id_incorrect'
            )->with(['options' => function($optionQuery) {
                $optionQuery->select(
                    'id',
                    'question_id',
                    'option_text',
                    'is_correct'
                );
            }]);
        }])->get();
});