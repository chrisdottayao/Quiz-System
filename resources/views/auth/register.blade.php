@extends('layouts.app')

@section('title', 'Register')

@section('content')
<div class="d-flex justify-content-center align-items-center" 
     style="min-height: 100vh; background: linear-gradient(135deg, #006400, #228B22); animation: fadeIn 1s ease-in;">
    <div class="card shadow-lg p-4 text-center" 
         style="width: 450px; border-radius: 15px; animation: slideUp 0.8s ease-in-out;">
        
        <img src="{{ asset('images/psau_logo.png') }}" 
             alt="PSAU Logo" 
             class="mx-auto mb-3" 
             style="width: 90px; height: 90px;">
             
        <h3 class="fw-bold text-success mb-2">Simple Quiz System</h3>
        <h6 class="text-muted mb-4">Pampanga State Agricultural University</h6>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="mb-3 text-start">
                <label for="name" class="form-label fw-semibold">Full Name</label>
                <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required autofocus>
            </div>

            <div class="mb-3 text-start">
                <label for="email" class="form-label fw-semibold">Email</label>
                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>
            </div>
            <div class="mt-4">
                <x-input-label for="role" :value="__('Select Role')" />
                <select id="role" name="role" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                    <option value="student">Student</option>
                    <option value="teacher">Teacher</option>
                </select>
            </div>
            <div class="mb-3 text-start">
                <label for="password" class="form-label fw-semibold">Password</label>
                <input id="password" type="password" class="form-control" name="password" required>
            </div>
            <div class="mb-3 text-start">
                <label for="password_confirmation" class="form-label fw-semibold">Confirm Password</label>
                <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" required>
            </div>

            <button type="submit" class="btn w-100 text-white" style="background-color: #FFD700; color: #006400; font-weight: bold;">Register</button>
        </form>

        <div class="text-center mt-3">
            <small>Already have an account? <a href="{{ route('login') }}" class="text-success fw-bold text-decoration-none">Login</a></small>
        </div>
    </div>
</div>

<style>
@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

@keyframes slideUp {
  from { transform: translateY(40px); opacity: 0; }
  to { transform: translateY(0); opacity: 1; }
}
</style>
@endsection
