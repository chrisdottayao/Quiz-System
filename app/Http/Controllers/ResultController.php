<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\Result;
use App\Models\User;
use Illuminate\Http\Request;

class ResultController extends Controller
{
    // Show all available quizzes for students to take (student view)
    public function index()
    {
        if (auth()->user()->role === 'teacher') {
            // For teachers, show quizzes they created or in their subjects
            $subjectIds = auth()->user()->subjectsTeaching->pluck('id');
            $quizzes = Quiz::where(function($q) use ($subjectIds) {
                $q->whereIn('subject_id', $subjectIds)
                  ->orWhere('user_id', auth()->id());
            })->latest()->get();

            return view('results.index', compact('quizzes'));
        }

        // Student: only quizzes from joined subjects and not closed
        $subjectIds = auth()->user()->subjectsJoined->pluck('id');

        $quizzes = Quiz::whereIn('subject_id', $subjectIds)
                       ->where(function($q){
                           $q->whereNull('deadline')->orWhere('deadline','>', now());
                       })
                       ->latest()->get();

        return view('results.index', compact('quizzes'));
    }

    // Show quiz questions for answering (student)
    public function show(Quiz $quiz)
    {
        $quiz->load('questions', 'subject');

        // If student, ensure enrolled and not expired
        if (auth()->user()->role === 'student') {
            if (!$quiz->subject || ! $quiz->subject->students->contains(auth()->id())) {
                abort(403, 'You are not enrolled in this subject.');
            }
            if ($quiz->deadline && now()->greaterThan($quiz->deadline)) {
                return redirect()->route('results.index')->with('error', 'This quiz has expired.');
            }
        }

        // Teacher can preview too (optionally)
        return view('results.take', compact('quiz'));
    }

    // Handle quiz submission (student)
    public function store(Request $request)
    {
        $request->validate([
            'quiz_id' => 'required|exists:quizzes,id',
            'answers' => 'array',
        ]);

        $quiz = Quiz::with('questions')->findOrFail($request->quiz_id);

        // verify student enrollment
        if (auth()->user()->role === 'student') {
            if (!$quiz->subject || ! $quiz->subject->students->contains(auth()->id())) {
                abort(403);
            }
            if ($quiz->deadline && now()->greaterThan($quiz->deadline)) {
                return redirect()->route('results.index')->with('error','This quiz has expired.');
            }
        }

        $score = 0;
        foreach ($quiz->questions as $question) {
            $userAnswer = $request->answers[$question->id] ?? null;
            if ($userAnswer && $userAnswer === $question->correct_option) {
                $score++;
            }
        }

        Result::create([
            'user_id' => auth()->id(),
            'quiz_id' => $quiz->id,
            'score' => $score,
        ]);

        return redirect()->route('results.myScores')->with('success','Quiz submitted! Your score: '.$score);
    }

    // Teacher: view all results for quizzes that belong to teacher (or their subjects)
    public function allResults()
    {
        if (auth()->user()->role !== 'teacher') {
            abort(403);
        }

        $results = Result::whereHas('quiz', function($q){
                $q->where('user_id', auth()->id())->orWhereHas('subject', function($s){
                    $s->where('teacher_id', auth()->id());
                });
            })
            ->with(['user','quiz'])
            ->latest()
            ->get();

        return view('results.all', compact('results'));
    }

    // Student: my scores
    public function myScores()
    {
        $user = auth()->user();
        $results = Result::with('quiz')
                    ->where('user_id', $user->id)
                    ->latest()
                    ->get();

        return view('results.my-scores', compact('results'));
    }

    // Teacher: view results of a single student but only for quizzes teacher owns
    public function viewStudentResults(User $user)
    {
        if (auth()->user()->role !== 'teacher') abort(403);

        $results = Result::with('quiz')
            ->where('user_id', $user->id)
            ->whereHas('quiz', function($q){
                $q->where('user_id', auth()->id())->orWhereHas('subject', function($s){
                    $s->where('teacher_id', auth()->id());
                });
            })
            ->latest()
            ->get();

        return view('teacher.view-student-results', compact('results', 'user'));
    }
}
