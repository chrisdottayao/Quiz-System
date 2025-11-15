<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
         \Illuminate\Support\Facades\Redirect::macro('intendedDashboard', function () {
        $user = Auth::user();

        if ($user && $user->role === 'teacher') {
            return redirect()->route('teacher.dashboard');
        } elseif ($user && $user->role === 'student') {
            return redirect()->route('student.dashboard');
        }

        return redirect('/login');
    });
    }
}
