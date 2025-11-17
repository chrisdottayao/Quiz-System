<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    public function index()
    {
        if (auth()->user()->role === 'teacher') {
            // Only quizzes from teacher's subjects (or quizzes the teacher created without subject)
            $subjectIds = auth()->user()->subjectsTeaching->pluck('id')->toArray();
            $quizzes = Quiz::where(function($q) use ($subjectIds) {
                $q->whereIn('subject_id', $subjectIds)
                  ->orWhere('user_id', auth()->id());
            })->latest()->get();
        } else {
            $subjectIds = auth()->user()->subjectsJoined->pluck('id');
            $quizzes = Quiz::whereIn('subject_id', $subjectIds)
                           ->where(function($q){
                               $q->whereNull('deadline')->orWhere('deadline','>', now());
                           })
                           ->latest()
                           ->get();
        }

        return view('quizzes.index', compact('quizzes'));
    }

    public function create()
    {
        $subjects = auth()->user()->subjectsTeaching;
        return view('quizzes.create', compact('subjects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'subject_id' => 'nullable|exists:subjects,id',
            'deadline' => 'nullable|date',
        ]);

        if ($request->subject_id) {
            $subject = Subject::findOrFail($request->subject_id);
            if ($subject->teacher_id !== auth()->id()) abort(403);
        }

        Quiz::create([
            'title' => $request->title,
            'description' => $request->description,
            'user_id' => auth()->id(),
            'subject_id' => $request->subject_id,
            'deadline' => $request->deadline,
        ]);

        return redirect()->route('quizzes.index')->with('success','Quiz created!');
    }

    public function show(Quiz $quiz)
    {
        $quiz->load('questions','subject');

        if (auth()->user()->role === 'student') {
            if (! $quiz->subject || ! $quiz->subject->students->contains(auth()->id())) {
                abort(403, 'You are not enrolled in this subject.');
            }
            if ($quiz->deadline && now()->greaterThan($quiz->deadline)) {
                return redirect()->route('results.index')->with('error','This quiz has expired.');
            }
        }

        return view('quizzes.show', compact('quiz'));
    }

    public function edit(Quiz $quiz)
    {
        if ($quiz->subject && $quiz->subject->teacher_id !== auth()->id()) abort(403);
        if (!$quiz->subject && $quiz->user_id !== auth()->id()) abort(403);

        $subjects = auth()->user()->subjectsTeaching;
        return view('quizzes.edit', compact('quiz','subjects'));
    }

    public function update(Request $request, Quiz $quiz)
    {
        if ($quiz->subject && $quiz->subject->teacher_id !== auth()->id()) abort(403);
        if (!$quiz->subject && $quiz->user_id !== auth()->id()) abort(403);

        $request->validate([
            'title'=>'required|string|max:255',
            'description'=>'nullable|string',
            'deadline'=>'nullable|date',
            'subject_id'=>'nullable|exists:subjects,id',
        ]);

        if ($request->subject_id) {
            $subject = Subject::findOrFail($request->subject_id);
            if ($subject->teacher_id !== auth()->id()) abort(403);
        }

        $quiz->update($request->only('title','description','deadline','subject_id'));

        return redirect()->route('quizzes.index')->with('success','Quiz updated!');
    }

    public function destroy(Quiz $quiz)
    {
        if ($quiz->subject && $quiz->subject->teacher_id !== auth()->id()) abort(403);
        if (!$quiz->subject && $quiz->user_id !== auth()->id()) abort(403);

        $quiz->delete();
        return redirect()->route('quizzes.index')->with('success','Quiz deleted!');
    }
}
