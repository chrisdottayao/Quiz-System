@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="d-flex justify-content-center align-items-center" 
     style="min-height: 100vh; background: linear-gradient(135deg, #006400, #228B22); animation: fadeIn 1s ease-in;">
    <div class="card shadow-lg p-4 text-center" 
         style="width: 400px; border-radius: 15px; animation: slideUp 0.8s ease-in-out;">
        
        <img src="{{ asset('images/psau_logo.png') }}" 
             alt="PSAU Logo" 
             class="mx-auto mb-3" 
             style="width: 90px; height: 90px;">
             
        <h3 class="fw-bold text-success mb-2">Simple Quiz System</h3>
        <h6 class="text-muted mb-4">Pampanga State Agricultural University</h6>

        @if(session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-3 text-start">
                <label for="email" class="form-label fw-semibold">Email</label>
                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus>
            </div>

            <div class="mb-3 text-start">
                <label for="password" class="form-label fw-semibold">Password</label>
                <input id="password" type="password" class="form-control" name="password" required>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <input type="checkbox" name="remember" id="remember">
                    <label for="remember" class="ms-1 small">Remember Me</label>
                </div>
                @if (Route::has('password.request'))
                    <a class="small text-success text-decoration-none" href="{{ route('password.request') }}">Forgot Password?</a>
                @endif
            </div>

            <button type="submit" class="btn w-100 text-white" style="background-color: #006400;">Login</button>
        </form>

        <div class="text-center mt-3">
            <small>Don’t have an account? <a href="{{ route('register') }}" class="text-success fw-bold text-decoration-none">Register</a></small>
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
