<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $user = Auth::user();

        $tasks = $user->tasks()->get();

        $response = Inertia::render('Tasks/index', [
            'tasks' => fn () => $tasks,
        ]);

        return $response;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request): void
    {
        $user = Auth::user();

        $user->tasks()->create($request->validated());
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task): void
    {
        //
        $this->authorize('update', $task);

        $task->update($request->validated());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task): void
    {
        //
        $this->authorize('delete', $task);

        $task->delete();
    }
}
