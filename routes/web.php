<?php
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TweetController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TweetLikeController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\MediaController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\PlayerRecordsViewController;

Route::post('/register', [AuthController::class, 'register']);
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
})->name('home');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/profile/{user}', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/tweets/search', [TweetController::class, 'search'])->name('tweets.search');
    Route::resource('tweets', TweetController::class);
    Route::post('/tweets/{tweet}/like', [TweetLikeController::class, 'store'])->name('tweets.like');
    Route::delete('/tweets/{tweet}/like', [TweetLikeController::class, 'destroy'])->name('tweets.dislike');
    Route::resource('tweets.comments', CommentController::class);
    Route::post('/follow/{user}', [FollowController::class, 'store'])->name('follow.store');
    Route::delete('/follow/{user}', [FollowController::class, 'destroy'])->name('follow.destroy');
    Route::resource('quizzes', QuizController::class);
    Route::get('/quizzes-manage', [QuizController::class, 'manage'])->name('quizzes.manage');
    Route::get('/quizzes/{quiz}/take', [QuizController::class, 'take'])->name('quizzes.take');
    Route::post('/quizzes/{quiz}/submit', [QuizController::class, 'submit'])->name('quizzes.submit');
    Route::get('/media', [MediaController::class, 'index'])->name('media.index');
    Route::post('/media', [MediaController::class, 'store'])->name('media.store');
    Route::put('/media', [MediaController::class, 'update'])->name('media.update');
    Route::delete('/media', [MediaController::class, 'destroy'])->name('media.destroy');
});

// 一時的なメディアファイルのテスト用ルート
Route::get('/test-media', function () {
    $files = [
        'videos' => \Illuminate\Support\Facades\Storage::disk('media')->files('videos'),
        'images' => \Illuminate\Support\Facades\Storage::disk('media')->files('images'),
    ];
    
    $videoUrls = [];
    foreach ($files['videos'] as $file) {
        $videoUrls[] = [
            'path' => $file,
            'url' => '/storage/media/' . $file,
            'exists' => file_exists(public_path('storage/media/' . $file)),
        ];
    }
    
    $imageUrls = [];
    foreach ($files['images'] as $file) {
        $imageUrls[] = [
            'path' => $file,
            'url' => '/storage/media/' . $file,
            'exists' => file_exists(public_path('storage/media/' . $file)),
        ];
    }
    
    return [
        'video_files' => $videoUrls,
        'image_files' => $imageUrls,
    ];
});

// プレイヤーの成績表示ルート
Route::prefix('player-records')->group(function () {
    Route::get('/', [PlayerRecordsViewController::class, 'index'])->name('player-records.index');
    Route::get('/{playerRecord}', [PlayerRecordsViewController::class, 'show'])->name('player-records.show');
});

Route::get('/users/{user}/player-records', [PlayerRecordsViewController::class, 'userRecords'])->name('player-records.user');
Route::get('/quizzes/{quiz}/player-records', [PlayerRecordsViewController::class, 'quizRecords'])->name('player-records.quiz');

require __DIR__.'/auth.php';
