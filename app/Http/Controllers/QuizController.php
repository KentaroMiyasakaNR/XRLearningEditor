<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
                'questions.*.question_text' => 'required|string',
                'questions.*.points' => 'required|integer|min:1',
                'questions.*.options' => 'required|array|min:2',
                'questions.*.options.*.option_text' => 'required|string',
                'questions.*.options.*.is_correct' => 'required|boolean',
            ]);

            $quiz = Quiz::create([
                'title' => $request->title,
                'description' => $request->description,
                'user_id' => Auth::id(),
            ]);

            \Log::info('Quiz created:', ['quiz_id' => $quiz->id]);

            foreach ($request->questions as $questionData) {
                $question = $quiz->questions()->create([
                    'question_text' => $questionData['question_text'],
                    'points' => $questionData['points'],
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
                ->with('success', 'クイズが作成されました。');

        } catch (\Exception $e) {
            \Log::error('Quiz creation failed:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

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
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $quiz->update($request->only(['title', 'description']));

        return redirect()->route('quizzes.show', $quiz)
            ->with('success', 'クイズが更新されました。');
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
        if ($quiz->user_id === Auth::id()) {
            return redirect()->route('quizzes.show', $quiz)
                ->with('error', '自分で作成したクイズは受験できません。');
        }

        $quiz->load('questions.options');
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
            ];
        }

        return view('quizzes.result', compact('quiz', 'results', 'totalPoints', 'earnedPoints'));
    }
}
