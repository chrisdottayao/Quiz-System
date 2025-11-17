<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\SubjectController;

// 🏠 Home Page
Route::get('/', function () {
    return view('welcome');
});

// 📋 Default Breeze Dashboard (not used anymore, but keep for safety)
//Route::get('/dashboard', function () {
//    return view('dashboard');
//})->middleware(['auth', 'verified'])->name('dashboard');

// 👤 Profile Routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::resource('classrooms', ClassroomController::class)->except(['show','edit','update','destroy']);
    Route::get('classrooms/join', [ClassroomController::class,'joinForm'])->name('classrooms.joinForm');
    Route::post('classrooms/join', [ClassroomController::class,'join'])->name('classrooms.join');
    Route::resource('subjects', SubjectController::class)->except(['show']);
    Route::get('subjects/join', [SubjectController::class,'joinForm'])->name('subjects.joinForm');
    Route::post('subjects/join', [SubjectController::class,'join'])->name('subjects.join');
    Route::get('subjects/{subject}/missed', [SubjectController::class,'missed'])->name('subjects.missed')->middleware('auth','role:teacher');
    Route::get('results', [ResultController::class,'index'])->name('results.index');
Route::get('results/{quiz}', [ResultController::class,'show'])->name('results.show');
Route::post('results', [ResultController::class,'store'])->name('results.store');
Route::get('all-results', [ResultController::class,'allResults'])->name('results.all')->middleware('role:teacher');
Route::get('my-scores', [ResultController::class,'myScores'])->name('results.myScores');
Route::get('teacher/student/{user}', [ResultController::class,'viewStudentResults'])->name('teacher.viewStudentResults')->middleware('role:teacher');
});

// 🔐 Role-Based Dashboards
Route::get('/teacher/dashboard', function () {
    return view('teacher.dashboard');
})->middleware('auth')->name('teacher.dashboard');

Route::get('/student/dashboard', function () {
    return view('student.dashboard');
})->middleware('auth')->name('student.dashboard');

// 🔄 Redirect After Login
Route::get('/redirect', function () {
    $user = Auth::user();

    if ($user->role === 'teacher') {
        return redirect()->route('teacher.dashboard');
    } else {
        return redirect()->route('student.dashboard');
    }
})->middleware('auth')->name('redirect');

// 🧑‍🏫 Teacher Routes (Quizzes, Questions, Results)
Route::middleware(['auth', 'role:teacher'])->group(function () {
    Route::resource('quizzes', QuizController::class);
    Route::resource('questions', QuestionController::class);
    Route::get('/all-results', [ResultController::class, 'allResults'])->name('results.all');
    Route::get('/teacher/results/{user}', [ResultController::class, 'viewStudentResults'])->name('teacher.viewStudentResults');
    Route::get('/results/all', [App\Http\Controllers\ResultController::class, 'allResults'])->name('results.all');
    // Export all results as PDF or Excel
Route::get('/results/export/pdf', [App\Http\Controllers\ResultController::class, 'exportPDF'])->name('results.exportPDF');
});

// 🎓 Student Routes (Taking Quizzes)
Route::middleware(['auth', 'role:student'])->group(function () {
    Route::resource('results', ResultController::class);
    Route::get('/my-scores', [ResultController::class, 'myScores'])->name('results.myScores');

});

require __DIR__.'/auth.php';

Route::get('/results/all/{quiz?}', [App\Http\Controllers\ResultController::class, 'allResults'])
    ->name('results.all');

Route::view('/about', 'about')->name('about');

Route::get('/profile', function () {
    return view('profile');
})->middleware('auth')->name('profile');

Route::get('/go-dashboard', function () {
    $user = auth()->user();
    return redirect()->route($user->role === 'teacher' ? 'teacher.dashboard' : 'student.dashboard');
})->middleware('auth')->name('go.dashboard');

Route::get('/whoami', function () {
    if (auth()->check()) {
        return 'Logged in as ' . auth()->user()->role;
    }
    return 'Not logged in';
});

Route::get('/session-test', function () {
    session(['psau' => 'works']);
    return session('psau');
});

