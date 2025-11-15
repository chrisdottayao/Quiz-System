<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    // Show all questions for a specific quiz
    public function index(Quiz $quiz)
    {
        $questions = $quiz->questions;
        return view('questions.index', compact('quiz', 'questions'));
    }

    // Show form to add a question
    public function create()
    {
        $quizzes = Quiz::all(); // So teacher can select which quiz to add to
        return view('questions.create', compact('quizzes'));
    }

    // Save a new question
    public function store(Request $request)
    {
        $request->validate([
            'quiz_id' => 'required|exists:quizzes,id',
            'question_text' => 'required',
            'option_a' => 'required',
            'option_b' => 'required',
            'correct_answer' => 'required',
        ]);

        Question::create([
            'quiz_id' => $request->quiz_id,
            'question_text' => $request->question_text,
            'option_a' => $request->option_a,
            'option_b' => $request->option_b,
            'option_c' => $request->option_c,
            'option_d' => $request->option_d,
            'correct_answer' => $request->correct_answer,
        ]);

        return redirect()->route('quizzes.show', $request->quiz_id);
    }
}
