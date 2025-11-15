<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class ClassroomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if(auth()->user()->role === 'teacher') {
        $classrooms = auth()->user()->classesTeaching()->latest()->get();
    } else {
        $classrooms = auth()->user()->classes()->latest()->get();
    }
    return view('classrooms.index', compact('classrooms'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
         return view('classrooms.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate(['name'=>'required|string|max:255','description'=>'nullable|string']);
    $class = Classroom::create([
        'name'=>$request->name,
        'description'=>$request->description,
        'teacher_id'=>auth()->id(),
        'code'=>Str::upper(Str::random(6))
    ]);
    return redirect()->route('classrooms.index')->with('success','Class created. Code: '.$class->code);
    }
    public function joinForm() {
    return view('classrooms.join');
}
public function join(Request $request) {
    $request->validate(['code'=>'required|string']);
    $class = Classroom::where('code', $request->code)->first();
    if(!$class) return back()->with('error','Invalid class code.');
    // attach if not already
    $class->students()->syncWithoutDetaching([auth()->id()]);
    return redirect()->route('classrooms.index')->with('success','Joined class '.$class->name);
}
    /**
     * Display the specified resource.
     */
    public function show(Classroom $classroom)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Classroom $classroom)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Classroom $classroom)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Classroom $classroom)
    {
        //
    }
}
