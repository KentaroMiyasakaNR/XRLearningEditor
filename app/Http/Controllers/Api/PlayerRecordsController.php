<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PlayerRecord;
use App\Models\QuestionRecord;
use App\Models\Quiz;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PlayerRecordsController extends Controller
{
    /**
     * プレイヤーの成績一覧を取得
     */
    public function index(Request $request): JsonResponse
    {
        $query = PlayerRecord::with(['user:id,name', 'quiz:id,title'])
            ->orderBy('created_at', 'desc');
        
        // フィルタリング
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        
        if ($request->has('quiz_id')) {
            $query->where('quiz_id', $request->quiz_id);
        }
        
        if ($request->has('from_date')) {
            $query->whereDate('completed_at', '>=', $request->from_date);
        }
        
        if ($request->has('to_date')) {
            $query->whereDate('completed_at', '<=', $request->to_date);
        }
        
        $records = $query->paginate(20);
        
        return response()->json($records);
    }
    
    /**
     * 特定ユーザーの成績一覧を取得
     */
    public function userRecords(User $user): JsonResponse
    {
        $records = PlayerRecord::with(['quiz:id,title'])
            ->where('user_id', $user->id)
            ->orderBy('completed_at', 'desc')
            ->paginate(20);
        
        return response()->json([
            'user' => $user->only(['id', 'name']),
            'records' => $records
        ]);
    }
    
    /**
     * 特定クイズの成績一覧を取得
     */
    public function quizRecords(Quiz $quiz): JsonResponse
    {
        $records = PlayerRecord::with(['user:id,name'])
            ->where('quiz_id', $quiz->id)
            ->orderBy('completed_at', 'desc')
            ->paginate(20);
        
        return response()->json([
            'quiz' => $quiz->only(['id', 'title', 'description']),
            'records' => $records
        ]);
    }
    
    /**
     * 成績の詳細を取得
     */
    public function show(PlayerRecord $playerRecord): JsonResponse
    {
        $playerRecord->load([
            'user:id,name',
            'quiz:id,title,description',
            'questionRecords.question:id,question_text',
            'questionRecords.option:id,option_text'
        ]);
        
        return response()->json($playerRecord);
    }
    
    /**
     * 新しい成績を登録
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'quiz_id' => 'required|exists:quizzes,id',
            'vr_session_id' => 'nullable|string',
            'completed_at' => 'required|date',
            'total_score' => 'required|integer|min:0',
            'time_taken' => 'nullable|integer|min:0',
            'question_records' => 'required|array',
            'question_records.*.question_id' => 'required|exists:questions,id',
            'question_records.*.option_id' => 'nullable|exists:options,id',
            'question_records.*.is_correct' => 'required|boolean',
            'question_records.*.time_taken' => 'nullable|integer|min:0',
            'question_records.*.points_earned' => 'required|integer|min:0',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $validated = $validator->validated();
        
        DB::beginTransaction();
        
        try {
            // プレイヤーレコードを作成
            $playerRecord = PlayerRecord::create([
                'user_id' => $validated['user_id'],
                'quiz_id' => $validated['quiz_id'],
                'vr_session_id' => $validated['vr_session_id'] ?? null,
                'completed_at' => $validated['completed_at'],
                'total_score' => $validated['total_score'],
                'time_taken' => $validated['time_taken'] ?? null,
            ]);
            
            // 質問ごとの記録を作成
            foreach ($validated['question_records'] as $record) {
                QuestionRecord::create([
                    'player_record_id' => $playerRecord->id,
                    'question_id' => $record['question_id'],
                    'option_id' => $record['option_id'] ?? null,
                    'is_correct' => $record['is_correct'],
                    'time_taken' => $record['time_taken'] ?? null,
                    'points_earned' => $record['points_earned'],
                ]);
            }
            
            DB::commit();
            
            return response()->json([
                'message' => 'Player record saved successfully',
                'record' => $playerRecord->load('questionRecords')
            ], 201);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'message' => 'Failed to save player record',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
