<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class QuizController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $quizzes = Quiz::with('questions.options')->latest()->paginate(10);
        return view('quizzes.index', compact('quizzes'));
    }

    /**
     * Display a listing of the user's quizzes for management.
     */
    public function manage()
    {
        $myQuizzes = Quiz::with('questions.options')
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);
        
        return view('quizzes.manage', compact('myQuizzes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('quizzes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        \Log::info('Quiz creation request:', $request->all());

        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'questions' => 'required|array|min:1',
                'questions.*.question_text' => 'required|string|max:1000',
                'questions.*.points' => 'required|integer|min:1',
                'questions.*.media_name' => 'nullable|string|max:255',
                'questions.*.explanation_text' => 'nullable|string',
                'questions.*.explanation_image_name' => 'nullable|string|max:255',
                'questions.*.options' => 'required|array|min:2',
                'questions.*.options.*.option_text' => 'required|string|max:500',
                'questions.*.options.*.is_correct' => 'required|boolean',
            ], [
                'title.required' => 'タイトルは必須です。',
                'title.max' => 'タイトルは255文字以内で入力してください。',
                'questions.required' => '質問は必須です。',
                'questions.min' => '少なくとも1つの質問を追加してください。',
                'questions.*.question_text.required' => '質問文は必須です。',
                'questions.*.question_text.max' => '質問文は1000文字以内で入力してください。',
                'questions.*.points.required' => '配点は必須です。',
                'questions.*.points.min' => '配点は1以上で入力してください。',
                'questions.*.media_name.max' => 'メディア名は255文字以内で入力してください。',
                'questions.*.explanation_text.max' => '説明テキストは255文字以内で入力してください。',
                'questions.*.explanation_image_name.max' => '説明画像名は255文字以内で入力してください。',
                'questions.*.options.required' => '選択肢は必須です。',
                'questions.*.options.min' => '選択肢は最低2つ必要です。',
                'questions.*.options.*.option_text.required' => '選択肢のテキストは必須です。',
                'questions.*.options.*.option_text.max' => '選択肢のテキストは500文字以内で入力してください。',
                'questions.*.options.*.is_correct.required' => '正解の選択は必須です。',
            ]);

            // 各質問に少なくとも1つの正解があることを確認
            foreach ($request->questions as $index => $question) {
                $hasCorrectAnswer = false;
                foreach ($question['options'] as $option) {
                    if ($option['is_correct']) {
                        $hasCorrectAnswer = true;
                        break;
                    }
                }
                if (!$hasCorrectAnswer) {
                    throw new \Exception("質問" . ($index + 1) . "には少なくとも1つの正解を選択してください。");
                }
            }

            $quiz = Quiz::create([
                'title' => $request->title,
                'description' => $request->description,
                'user_id' => Auth::id(),
            ]);

            foreach ($request->questions as $questionData) {
                $question = $quiz->questions()->create([
                    'question_text' => $questionData['question_text'],
                    'points' => $questionData['points'],
                    'media_name' => $questionData['media_name'] ?? null,
                    'explanation_text' => $questionData['explanation_text'] ?? null,
                    'explanation_image_name' => $questionData['explanation_image_name'] ?? null,
                ]);

                foreach ($questionData['options'] as $optionData) {
                    $question->options()->create([
                        'option_text' => $optionData['option_text'],
                        'is_correct' => $optionData['is_correct'],
                    ]);
                }
            }

            if ($request->ajax()) {
                return response()->json([
                    'message' => 'クイズが作成されました。',
                    'redirect' => route('quizzes.index')
                ]);
            }

            return redirect()->route('quizzes.index')
                ->with('success', 'クイズが作成されました。');

        } catch (\Exception $e) {
            \Log::error('Quiz creation failed:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'message' => 'クイズの作成に失敗しました。',
                    'errors' => ['error' => [$e->getMessage()]]
                ], 422);
            }

            return back()
                ->withInput()
                ->withErrors(['error' => 'クイズの作成に失敗しました。: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Quiz $quiz)
    {
        $quiz->load('questions.options');
        return view('quizzes.show', compact('quiz'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Quiz $quiz)
    {
        $quiz->load('questions.options');
        return view('quizzes.edit', compact('quiz'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Quiz $quiz)
    {
        \Log::info('Quiz update request:', $request->all());

        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'questions' => 'required|array|min:1',
                'questions.*.question_text' => 'required|string|max:1000',
                'questions.*.points' => 'required|integer|min:1',
                'questions.*.media_name' => 'nullable|string|max:255',
                'questions.*.explanation_text' => 'nullable|string',
                'questions.*.explanation_image_name' => 'nullable|string|max:255',
                'questions.*.options' => 'required|array|min:2',
                'questions.*.options.*.option_text' => 'required|string|max:500',
                'questions.*.options.*.is_correct' => 'required|boolean',
            ], [
                'title.required' => 'タイトルは必須です。',
                'title.max' => 'タイトルは255文字以内で入力してください。',
                'questions.required' => '質問は必須です。',
                'questions.min' => '少なくとも1つの質問を追加してください。',
                'questions.*.question_text.required' => '質問文は必須です。',
                'questions.*.question_text.max' => '質問文は1000文字以内で入力してください。',
                'questions.*.points.required' => '配点は必須です。',
                'questions.*.points.min' => '配点は1以上で入力してください。',
                'questions.*.media_name.max' => 'メディア名は255文字以内で入力してください。',
                'questions.*.explanation_text.max' => '説明テキストは255文字以内で入力してください。',
                'questions.*.explanation_image_name.max' => '説明画像名は255文字以内で入力してください。',
                'questions.*.options.required' => '選択肢は必須です。',
                'questions.*.options.min' => '選択肢は最低2つ必要です。',
                'questions.*.options.*.option_text.required' => '選択肢のテキストは必須です。',
                'questions.*.options.*.option_text.max' => '選択肢のテキストは500文字以内で入力してください。',
                'questions.*.options.*.is_correct.required' => '正解の選択は必須です。',
            ]);

            // 各質問に少なくとも1つの正解があることを確認
            foreach ($request->questions as $index => $question) {
                $hasCorrectAnswer = false;
                foreach ($question['options'] as $option) {
                    if ($option['is_correct']) {
                        $hasCorrectAnswer = true;
                        break;
                    }
                }
                if (!$hasCorrectAnswer) {
                    throw new \Exception("質問" . ($index + 1) . "には少なくとも1つの正解を選択してください。");
                }
            }

            $quiz->update([
                'title' => $request->title,
                'description' => $request->description,
            ]);

            // 既存の質問と選択肢を削除
            $quiz->questions()->each(function ($question) {
                $question->options()->delete();
                $question->delete();
            });

            // 新しい質問と選択肢を作成
            foreach ($request->questions as $questionData) {
                $question = $quiz->questions()->create([
                    'question_text' => $questionData['question_text'],
                    'points' => $questionData['points'],
                    'media_name' => $questionData['media_name'] ?? null,
                    'explanation_text' => $questionData['explanation_text'] ?? null,
                    'explanation_image_name' => $questionData['explanation_image_name'] ?? null,
                ]);

                \Log::info('Question created:', [
                    'quiz_id' => $quiz->id,
                    'question_id' => $question->id,
                    'data' => $questionData
                ]);

                foreach ($questionData['options'] as $optionData) {
                    $option = $question->options()->create([
                        'option_text' => $optionData['option_text'],
                        'is_correct' => $optionData['is_correct'],
                    ]);

                    \Log::info('Option created:', [
                        'quiz_id' => $quiz->id,
                        'question_id' => $question->id,
                        'option_id' => $option->id,
                        'data' => $optionData
                    ]);
                }
            }

            return redirect()->route('quizzes.index')
                ->with('success', 'クイズが更新されました。');

        } catch (\Exception $e) {
            \Log::error('Quiz update failed:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'クイズの更新に失敗しました。: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Quiz $quiz)
    {
        $quiz->delete();
        return redirect()->route('quizzes.index')
            ->with('success', 'クイズが削除されました。');
    }

    public function take(Quiz $quiz)
    {
        $quiz->load('questions.options');
        
        foreach($quiz->questions as $question) {
            if ($question->media_name) {
                // シンプルに直接パスを使用
                $question->media_url = '/storage/media/videos/' . $question->media_name;
            }
            
            if ($question->explanation_image_name) {
                // シンプルに直接パスを使用
                $question->explanation_image_url = '/storage/media/images/' . $question->explanation_image_name;
            }
        }
        
        return view('quizzes.take', compact('quiz'));
    }

    public function submit(Request $request, Quiz $quiz)
    {
        $request->validate([
            'answers' => 'required|array',
            'answers.*' => 'required|array',
            'answers.*.*' => 'required|boolean',
        ]);

        $quiz->load('questions.options');
        $totalPoints = 0;
        $earnedPoints = 0;
        $results = [];

        foreach ($quiz->questions as $question) {
            if ($question->media_name) {
                // シンプルに直接パスを使用
                $question->media_url = '/storage/media/videos/' . $question->media_name;
            }
            
            if ($question->explanation_image_name) {
                // シンプルに直接パスを使用
                $question->explanation_image_url = '/storage/media/images/' . $question->explanation_image_name;
            }
            
            $totalPoints += $question->points;
            $correct = true;
            $questionResults = [];

            foreach ($question->options as $option) {
                $answered = isset($request->answers[$question->id][$option->id]) && 
                           $request->answers[$question->id][$option->id];
                
                if ($answered !== $option->is_correct) {
                    $correct = false;
                }

                $questionResults[$option->id] = [
                    'option_text' => $option->option_text,
                    'is_correct' => $option->is_correct,
                    'user_answer' => $answered,
                ];
            }

            if ($correct) {
                $earnedPoints += $question->points;
            }

            $results[$question->id] = [
                'question_text' => $question->question_text,
                'points' => $question->points,
                'earned_points' => $correct ? $question->points : 0,
                'options' => $questionResults,
                'is_correct' => $correct,
            ];
        }

        return view('quizzes.result', compact('quiz', 'results', 'totalPoints', 'earnedPoints'));
    }
}
