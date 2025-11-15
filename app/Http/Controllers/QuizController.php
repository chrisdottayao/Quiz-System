<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    // ------------------------
    // QUIZ LIST
    // ------------------------
    public function index()
    {
        if (auth()->user()->role === 'teacher') {
            // Only quizzes from teacher's subjects
            $subjectIds = auth()->user()->subjectsTeaching->pluck('id');

            $quizzes = Quiz::whereIn('subject_id', $subjectIds)
                           ->latest()
                           ->get();
        } 
        else {
            // Students: quizzes from subjects they joined AND not expired
            $subjectIds = auth()->user()->subjectsJoined->pluck('id');

            $quizzes = Quiz::whereIn('subject_id', $subjectIds)
                           ->where(function ($q) {
                               $q->whereNull('deadline')
                                 ->orWhere('deadline', '>', now());
                           })
                           ->latest()
                           ->get();
        }

        return view('quizzes.index', compact('quizzes'));
    }

    // ------------------------
    // CREATE QUIZ FORM
    // ------------------------
    public function create()
    {
        // Teachers can only create quizzes in their own subjects
        $subjects = auth()->user()->subjectsTeaching;

        return view('quizzes.create', compact('subjects'));
    }

    // ------------------------
    // STORE QUIZ
    // ------------------------
    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'subject_id'  => 'required|exists:subjects,id',
            'deadline'    => 'nullable|date',
        ]);

        // Ensure teacher owns the subject
        $this->authorizeSubjectOwner($request->subject_id);

        Quiz::create([
            'title'       => $request->title,
            'description' => $request->description,
            'user_id'     => auth()->id(),
            'subject_id'  => $request->subject_id,
            'deadline'    => $request->deadline,
        ]);

        return redirect()->route('quizzes.index')
                         ->with('success', 'Quiz created successfully!');
    }

    // ------------------------
    // SHOW QUIZ (student view)
    // ------------------------
    public function show(Quiz $quiz)
    {
        $quiz->load('questions', 'subject');

        if (auth()->user()->role === 'student') {
            if (! auth()->user()->subjectsJoined->contains($quiz->subject_id)) {
                abort(403, 'You are not enrolled in this subject.');
            }
        }

        return view('quizzes.show', compact('quiz'));
    }

    // ------------------------
    // EDIT QUIZ
    // ------------------------
    public function edit(Quiz $quiz)
    {
        $this->authorizeSubjectOwner($quiz->subject_id);

        $subjects = auth()->user()->subjectsTeaching;

        return view('quizzes.edit', compact('quiz', 'subjects'));
    }

    // ------------------------
    // UPDATE QUIZ
    // ------------------------
    public function update(Request $request, Quiz $quiz)
    {
        $this->authorizeSubjectOwner($quiz->subject_id);

        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'deadline'    => 'nullable|date',
        ]);

        $quiz->update($request->only('title', 'description', 'deadline'));

        return redirect()->route('quizzes.index')
                         ->with('success', 'Quiz updated successfully!');
    }

    // ------------------------
    // DELETE QUIZ
    // ------------------------
    public function destroy(Quiz $quiz)
    {
        $this->authorizeSubjectOwner($quiz->subject_id);

        $quiz->delete();

        return redirect()->route('quizzes.index')
                         ->with('success', 'Quiz deleted successfully!');
    }

    // ------------------------
    // CHECK IF TEACHER OWNS SUBJECT
    // ------------------------
    private function authorizeSubjectOwner($subjectId)
    {
        $subject = Subject::findOrFail($subjectId);

        if (auth()->user()->role !== 'teacher' ||
            $subject->teacher_id !== auth()->id()) 
        {
            abort(403, 'Unauthorized.');
        }
    }
}
