<?php

namespace App\Models;

use Attribute;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * @description WIPボード上に表示するタスクの取得とUIをDBに反映する
 */
class Board extends Model
{
    use HasUuids;

    // tasksテーブルを参照する
    protected $table = 'tasks';

    protected $fillable = [
        'status',
        'started_at',
        'completed_at',
        'is_today_task',
    ];

    public const STATUS = Task::STATUS;

    public static function status(): Attribute
    {
        return Task::status();
    }

    /**
     * statusが指定された値のタスクを取得する
     * 複数のstatusを指定することも可能
     *
     * @param  User  $user ユーザー
     * @param  int[] $statuses ステータスの配列;
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getMatchedStatusTasks(User $user, array $statuses): Collection
    {
        return Task::query()
            ->where('user_id', $user->id)
            ->whereIn('status', $statuses)
            ->latest('deadline')->get();
    }

    /**
     * is_today_taskがtrueかつ、ログインユーザーのタスクを取得する
     *
     * @param  User  $user ユーザー
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getTodayTasks(User $user): Collection
    {
        return Task::query()
            ->where('user_id', $user->id)
            ->where('status', 0) // 未着手
            ->where('is_today_task', true)
            ->latest('deadline')->get();
    }

    /**
     * completed_atが指定した日にち以内のかつログインユーザーのタスクを取得する
     *
     * 基本は5日前までのタスクを取得する
     *
     * @param  User  $user ユーザー
     * @param  int  $days 日数
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getRecentlyCompletedTasks(User $user, int $days = 5): Collection
    {
        $date = Carbon::today()->subDays($days);

        return Task::query()
            ->where('user_id', $user->id)
            ->where('status', 3) // 完了
            ->where('completed_at', '>=', $date)
            ->latest('completed_at')->get();
    }

    /**
     * deadlineが今日から指定した日にち以内のタスクを取得する
     *
     * 基本は5日前までのタスクを取得する
     *
     * @param  User  $user ユーザー
     * @param  int  $days 日数
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getRecentDeadlineTasks(User $user, int $days = 5): Collection
    {
        $date = Carbon::today()->endOfDay()->addDays($days);

        return Task::query()
            ->where('user_id', $user->id)
            ->where('status', 0) // 未着手
            ->where('deadline', '<=', $date)
            ->where('deadline', '>', Carbon::now())
            ->where('is_today_task', false)
            ->latest('deadline')->get();
    }

    /**
     * deadlineが過ぎている、かつ完了していないタスクを取得する
     *
     * @param  User  $user ユーザー
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getOverDeadlineTasks(User $user): Collection
    {
        return Task::query()
            ->where('user_id', $user->id)
            ->where('status', 0) // 未着手
            ->where('deadline', '<', Carbon::now())
            ->where('is_today_task', false)
            ->latest('deadline')->get();
    }

    /**
     * タスクを今日実行するタスクに追加する
     *
     * @param    $task App\Models\Task
     */
    public function enqueueTodayTask(Task $task): bool
    {
        // 今日実行するタスクに追加済みのタスクは追加できない
        if ($task->is_today_task['boolean']) {
            return false;
        }

        // 未着手のタスクのみ追加できる
        if ($task->status['label'] !== Task::STATUS[0]['label']) {
            return false;
        }

        $task->update([
            'is_today_task' => true,
        ]);

        return true;
    }

    /**
     * タスクを今日実行するタスクから外す
     *
     * @param    $task App\Models\Task
     */
    public function dequeueTodayTask(Task $task): bool
    {
        // 今日実行するタスクでないタスクは外せない
        if (!$task->is_today_task['boolean']) {
            return false;
        }

        // 未着手のタスクのみ外せる
        if (!$task->is_today_task['boolean']) {
            return false;
        }

        $task->update([
            'is_today_task' => false,
        ]);

        return true;
    }

    /**
     * タスクを進行中に移す
     *
     * @param    $task App\Models\Task
     */
    public function putInProgressTask(Task $task): bool
    {
        // 今日実行するタスクのみ進行中にできる
        if (!$task->is_today_task['boolean']) {
            return false;
        }

        // statusを進行中にする
        $task->update([
            'status' => Task::STATUS[1]['label'],
            'started_at' => Carbon::now(),
        ]);

        return true;
    }

    /**
     * タスクを完了にする
     *
     * @param    $task App\Models\Task
     */
    public function putCompletedTask(Task $task): bool
    {
        // 進行中のタスクのみ完了にできる
        if ($task->status['label'] !== Task::STATUS[1]['label']) {
            return false;
        }

        // statusを完了にする
        $task->update([
            'status' => Task::STATUS[3]['label'],
            'completed_at' => Carbon::now(),
            'is_today_task' => false,
        ]);

        return true;
    }

    /**
     * タスクを保留にする
     *
     * @param    $task App\Models\Task
     */
    public function putOnHoldTask(Task $task): bool
    {
        // 進行中のタスクのみ保留にできる
        if ($task->status['label'] !== Task::STATUS[1]['label']) {
            return false;
        }

        // statusを保留にする
        $task->update([
            'status' => Task::STATUS[2]['label'],
        ]);

        return true;
    }
}
