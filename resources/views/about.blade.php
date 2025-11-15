@extends('layouts.app')

@section('title', 'About the System')

@section('content')
<div class="container py-5">
    <div class="card shadow-sm">
        <div class="card-header bg-warning text-dark text-center">
            <h3>About the Simple Quiz System</h3>
        </div>
        <div class="card-body">
            <p>The <strong>Simple Quiz System</strong> was developed to make student assessments faster, paperless, and more efficient.</p>

            <ul>
                <li>🧠 Teachers can create and manage quizzes easily.</li>
                <li>💻 Students can take quizzes and view their scores instantly.</li>
                <li>📊 Results are saved automatically and viewable anytime.</li>
                <li>🧾 Teachers can export results as PDF or Excel-lite files.</li>
            </ul>

            <p>This project was created by <strong>Cristian N. Tayao and Others</strong> as part of the final coursework for <em>Pampanga State Agricultural University</em>.</p>

            <a href="{{ route('go.dashboard') }}" class="btn btn-success mt-3">⬅ Back to Dashboard</a>
        </div>
    </div>
</div>
@endsection
