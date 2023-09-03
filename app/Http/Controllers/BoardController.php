<?php

namespace App\Http\Controllers;

use App\Http\Requests\DequeueTodayTaskRequest;
use App\Http\Requests\EnqueueTodayTaskRequest;
use App\Http\Requests\PutCompletedTaskRequest;
use App\Http\Requests\PutInProgressTaskRequest;
use App\Http\Requests\PutOnHoldTaskRequest;
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
        $board = new Board();

        return Inertia::render('Boards/index', [
            'todayTasks' => $board->getTodayTasks($user),
            'onHoldTasks' => $board->getMatchedStatusTasks($user, [2]),
            'inProgressTask' => $board->getMatchedStatusTasks($user, [1]),
            'recentDeadlineTasks' => $board->getRecentDeadlineTasks($user),
            'recentlyCompletedTasks' => $board->getRecentlyCompletedTasks($user),
            'overDeadlineTasks' => $board->getOverDeadlineTasks($user),
            'statuses' => Task::STATUS,
        ]);
    }

    /**
     * タスクを今日実行するタスクに追加する
     *
     * @param  $request \App\Http\Requests\EnqueueTodayTaskRequest
     * @param  $task \App\Models\Task
     */
    public function enqueueTodayTask(EnqueueTodayTaskRequest $request, Task $task): void
    {
        // TaskPolicyのupdateメソッドを呼び出す
        $this->authorize('update', $task);

        $user = Auth::user();;
        $board = new Board();

        if ($request->validated()['is_today_task']) {
            $board->enqueueTodayTask($task);
        }
    }

    /**
     * タスクを今日実行するタスクから削除する
     *
     * @param  $request \App\Http\Requests\EnqueueTodayTaskRequest
     * @param  $task \App\Models\Task
     */
    public function dequeueTodayTask(DequeueTodayTaskRequest $request, Task $task): void
    {
        // TaskPolicyのupdateメソッドを呼び出す
        $this->authorize('update', $task);

        $user = Auth::user();;
        $board = new Board();

        if (!$request->validated()['is_today_task']) {
            $board->dequeueTodayTask($task);
        }
    }

    /**
     * タスクを進行中に移す
     *
     * @param  $request \App\Http\Requests\PutInProgressTaskRequest
     * @param  $task \App\Models\Task
     */
    public function putInProgressTask(PutInProgressTaskRequest $request, Task $task): void
    {
        // TaskPolicyのupdateメソッドを呼び出す
        $this->authorize('update', $task);

        $user = Auth::user();;
        $board = new Board();

        if ($request->validated()['status'] === Task::STATUS[1]['label']) {
            $board->putInProgressTask($user, $task);
        }
    }

    public function putOnHoldTask(PutOnHoldTaskRequest $request, Task $task): void
    {
        // TaskPolicyのupdateメソッドを呼び出す
        $this->authorize('update', $task);

        $user = Auth::user();;
        $board = new Board();

        if ($request->validated()['status'] === Task::STATUS[2]['label']) {
            $board->putOnHoldTask($task);
        }
    }

    /**
     * タスクを完了状態にする
     *
     * @param  $request \App\Http\Requests\EnqueueTodayTaskRequest
     * @param  $task \App\Models\Task
     */
    public function putCompletedTask(PutCompletedTaskRequest $request, Task $task): void
    {
        // TaskPolicyのupdateメソッドを呼び出す
        $this->authorize('update', $task);

        $user = Auth::user();;
        $board = new Board();

        if ($request->validated()['status'] === Task::STATUS[3]['label']) {
            $board->putCompletedTask($task);
        }
    }
}
