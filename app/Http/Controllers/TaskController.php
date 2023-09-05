<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Activity;
use App\Models\Task;
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

        $tasks = $user->tasks()->orderBy('deadline', 'asc')->get();

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

        $task = $user->tasks()->create($request->validated());

        // 作成のアクティビティを記録
        Activity::create([
            'user_id' => $user->id,
            'task_id' => $task->id,
            'type' => 0,
        ]);

        // 今日実行するタスクの追加のアクティビティを記録
        Activity::recordIsTodayTask($user, $task);

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

        $validated = $request->validated();

        $previousIsTodayTask = $task->is_today_task['boolean'];

        $task->update($validated);


        // 更新のアクティビティを記録
        Activity::create([
            'user_id' => Auth::user()->id,
            'task_id' => $task->id,
            'type' => 6,
        ]);

        // 今日実行するタスクの追加・削除のアクティビティを記録
        Activity::recordIsTodayTask(Auth::user(), $task, $previousIsTodayTask);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task): void
    {
        //
        $this->authorize('delete', $task);

        $task->delete();

        // タスクに紐づくアクティビティを削除
        Activity::where('task_id', $task->id)->delete();
    }
}
