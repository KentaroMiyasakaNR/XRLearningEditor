<?php

namespace App\Http\Controllers;

use App\Models\PlayerRecord;
use App\Models\Quiz;
use App\Models\User;
use Illuminate\Http\Request;

class PlayerRecordsViewController extends Controller
{
    /**
     * プレイヤーの成績一覧ページを表示
     */
    public function index(Request $request)
    {
        $quizzes = Quiz::select('id', 'title')->get();
        $users = User::select('id', 'name')->get();
        
        $query = PlayerRecord::with(['user:id,name', 'quiz:id,title'])
            ->orderBy('created_at', 'desc');
        
        // フィルタリング
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        
        if ($request->filled('quiz_id')) {
            $query->where('quiz_id', $request->quiz_id);
        }
        
        if ($request->filled('from_date')) {
            $query->whereDate('completed_at', '>=', $request->from_date);
        }
        
        if ($request->filled('to_date')) {
            $query->whereDate('completed_at', '<=', $request->to_date);
        }
        
        $records = $query->paginate(20);
        
        return view('player-records.index', [
            'records' => $records,
            'quizzes' => $quizzes,
            'users' => $users,
            'filters' => $request->only(['user_id', 'quiz_id', 'from_date', 'to_date'])
        ]);
    }
    
    /**
     * プレイヤーの成績詳細ページを表示
     */
    public function show(PlayerRecord $playerRecord)
    {
        $playerRecord->load([
            'user:id,name',
            'quiz:id,title,description',
            'questionRecords.question:id,question_text',
            'questionRecords.option:id,option_text'
        ]);
        
        // 正解数と正答率を計算
        $totalQuestions = $playerRecord->questionRecords->count();
        $correctAnswers = $playerRecord->questionRecords->where('is_correct', true)->count();
        $correctRate = $totalQuestions > 0 ? ($correctAnswers / $totalQuestions) * 100 : 0;
        
        return view('player-records.show', [
            'record' => $playerRecord,
            'totalQuestions' => $totalQuestions,
            'correctAnswers' => $correctAnswers,
            'correctRate' => $correctRate
        ]);
    }
    
    /**
     * 特定ユーザーの成績一覧ページを表示
     */
    public function userRecords(User $user)
    {
        $records = PlayerRecord::with(['quiz:id,title'])
            ->where('user_id', $user->id)
            ->orderBy('completed_at', 'desc')
            ->paginate(20);
        
        return view('player-records.user', [
            'user' => $user,
            'records' => $records
        ]);
    }
    
    /**
     * 特定クイズの成績一覧ページを表示
     */
    public function quizRecords(Quiz $quiz)
    {
        $records = PlayerRecord::with(['user:id,name'])
            ->where('quiz_id', $quiz->id)
            ->orderBy('completed_at', 'desc')
            ->paginate(20);
        
        return view('player-records.quiz', [
            'quiz' => $quiz,
            'records' => $records
        ]);
    }
}
