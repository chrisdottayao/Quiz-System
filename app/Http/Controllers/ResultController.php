<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\Question;
use App\Models\Result;
use Illuminate\Http\Request;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ResultController extends Controller
{
    // Show all available quizzes for students to take
    public function index()
    {
$classIds = auth()->user()->classes()->pluck('classrooms.id');

$quizzes = Quiz::whereIn('classroom_id', $classIds)
               ->where(function($q){
                    $q->whereNull('deadline')
                      ->orWhere('deadline', '>', now());
               })
               ->latest()
               ->get();

return view('results.index', compact('quizzes'));
    }

    // Show quiz questions for answering
public function show(Quiz $result)
{
    $quiz = $result;
     $quiz->load('questions', 'classroom');

    // Restrict unauthorized students
    if (auth()->user()->role === 'student') {
        if (! $quiz->classroom->students->contains(auth()->id())) {
            abort(403, 'You are not enrolled in this class.');
        }
    }

    if ($quiz->deadline && now()->greaterThan($quiz->deadline)) {
        return redirect()->route('results.index')
        ->with('error', '⏰ This quiz has expired.');
    }

    return view('results.take', compact('quiz'));
}
    // Handle quiz submission
    public function store(Request $request)
    {
        $quiz = \App\Models\Quiz::with('questions')->findOrFail($request->quiz_id);
        $score = 0;

        foreach ($quiz->questions as $question) {
            $userAnswer = $request->answers[$question->id] ?? null;
            if ($userAnswer && $userAnswer === $question->correct_option) {
                $score++;
            }
        }

        \App\Models\Result::create([
            'user_id' => auth()->id(),
            'quiz_id' => $quiz->id,
            'score' => $score,
        ]);

        return redirect()->route('results.myScores')
                         ->with('success', 'Quiz submitted successfully! Your score: ' . $score);
    }

    // ✅ Show all results (for teachers)
public function allResults()
{
    if (Auth::user()->role === 'teacher') {
        $results = Result::whereHas('quiz', function ($q) {
            $q->where('user_id', Auth::id());
        })->with(['quiz', 'user'])->get();
    } else {
        abort(403);
    }

    return view('results.all', compact('results'));
}

    // ✅ Show only logged-in student’s scores
    public function myScores()
    {
        $user = auth()->user();
        $results = \App\Models\Result::with('quiz')
                    ->where('user_id', $user->id)
                    ->latest()
                    ->get();

        return view('results.my-scores', compact('results'));
    }

    // ✅ Show one specific student’s results
public function viewStudentResults(User $user)
{
    $results = Result::where('user_id', $user->id)
        ->whereHas('quiz', function ($q) {
            $q->where('user_id', Auth::id());
        })
        ->with('quiz')
        ->get();

    return view('teacher.view-student-results', compact('results', 'user'));
}

    // ✅ Export Results as PDF
public function exportPDF()
{
    $results = \App\Models\Result::with(['user', 'quiz'])->latest()->get();

    $pdf = Pdf::loadView('results.export-pdf', compact('results'));

    return $pdf->download('quiz_results.pdf');
}

// ✅ Export Results as Excel
public function exportExcel()
{
    $results = \App\Models\Result::with(['user', 'quiz'])->latest()->get();

    $data = $results->map(function ($result) {
        return [
            'Student Name' => $result->user->name,
            'Quiz Title'   => $result->quiz->title,
            'Score'        => $result->score,
            'Date Taken'   => $result->created_at->format('Y-m-d H:i'),
        ];
    });

    return Excel::download(new class($data) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings {
        protected $data;
        public function __construct($data) { $this->data = $data; }
        public function collection() { return collect($this->data); }
        public function headings(): array {
            return ['Student Name', 'Quiz Title', 'Score', 'Date Taken'];
        }
    }, 'quiz_results.xlsx');
}

}
