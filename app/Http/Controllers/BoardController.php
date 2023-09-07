<?php

namespace App\Http\Controllers;

use App\Http\Requests\DequeueTodayTaskRequest;
use App\Http\Requests\EnqueueTodayTaskRequest;
use App\Http\Requests\PutCompletedTaskRequest;
use App\Http\Requests\PutInProgressTaskRequest;
use App\Http\Requests\PutOnHoldTaskRequest;
use App\Models\Activity;
use App\Models\Board;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class BoardController extends Controller
{
    public function index(): Response
    {
        $user = Auth::user();

        return Inertia::render('Boards/index', [
            'todayTasks' => Board::getTodayTasks($user),
            'onHoldTasks' => Board::getMatchedStatusTasks($user, [2]),
            'inProgressTask' => Board::getMatchedStatusTasks($user, [1]),
            'recentDeadlineTasks' => Board::getRecentDeadlineTasks($user),
            'recentlyCompletedTasks' => Board::getRecentlyCompletedTasks($user),
            'overDeadlineTasks' => Board::getOverDeadlineTasks($user),
            'statuses' => Task::STATUS,
        ]);
    }

    /**
     * タスクを今日実行するタスクに追加する
     *
     * @param    $request \App\Http\Requests\EnqueueTodayTaskRequest
     * @param    $task \App\Models\Task
     */
    public function enqueueTodayTask(EnqueueTodayTaskRequest $request, Task $task): void
    {
        // TaskPolicyのupdateメソッドを呼び出す
        $this->authorize('update', $task);

        $user = Auth::user();

        if ($request->validated()) {
            Board::enqueueTodayTask($task);
        }

        // 今日実行するタスクに追加のアクティビティを記録
        Activity::create([
            'task_id' => $task->id,
            'type' => 0,
        ]);

    }

    /**
     * タスクを今日実行するタスクから削除する
     *
     * @param    $request \App\Http\Requests\EnqueueTodayTaskRequest
     * @param    $task \App\Models\Task
     */
    public function dequeueTodayTask(DequeueTodayTaskRequest $request, Task $task): void
    {
        // TaskPolicyのupdateメソッドを呼び出す
        $this->authorize('update', $task);

        $user = Auth::user();

        if ($request->validated()) {
            Board::dequeueTodayTask($task);
        }

        // 今日実行するタスクから削除のアクティビティを記録
        Activity::create([
            'task_id' => $task->id,
            'type' => 1,
        ]);
    }

    /**
     * タスクを進行中に移す
     *
     * @param    $request \App\Http\Requests\PutInProgressTaskRequest
     * @param    $task \App\Models\Task
     */
    public function putInProgressTask(PutInProgressTaskRequest $request, Task $task): void
    {
        // TaskPolicyのupdateメソッドを呼び出す
        $this->authorize('update', $task);

        $user = Auth::user();

        $previousTask = Task::find($task->id);

        if ($request->validated()) {
            Board::putInProgressTask($user, $task);
        }


        // 進行中に移したアクティビティを記録
        Activity::recordInProgressTask($previousTask);
    }

    public function putOnHoldTask(PutOnHoldTaskRequest $request, Task $task): void
    {
        // TaskPolicyのupdateメソッドを呼び出す
        $this->authorize('update', $task);

        $user = Auth::user();

        if ($request->validated()) {
            Board::putOnHoldTask($task);
        }

        // 保留のアクティビティを記録
        Activity::create([
            'task_id' => $task->id,
            'type' => 4,
        ]);

    }

    /**
     * タスクを完了状態にする
     *
     * @param    $request \App\Http\Requests\EnqueueTodayTaskRequest
     * @param    $task \App\Models\Task
     */
    public function putCompletedTask(PutCompletedTaskRequest $request, Task $task): void
    {
        // TaskPolicyのupdateメソッドを呼び出す
        $this->authorize('update', $task);

        $user = Auth::user();

        if ($request->validated()) {
            Board::putCompletedTask($task);
        }

        // 完了のアクティビティを記録
        Activity::create([
            'task_id' => $task->id,
            'type' => 5,
        ]);
    }
}
