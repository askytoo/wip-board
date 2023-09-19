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
     * @param  int[]  $statuses ステータスの配列;
     * @return Illuminate\Database\Eloquent\Collection
     */
    public static function getMatchedStatusTasks(User $user, array $statuses): Collection
    {
        return Task::query()
            ->where('user_id', $user->id)
            ->whereIn('status', $statuses)
            ->with(['activities' => function ($query) {
                $query->orderBy('created_at', 'asc');
            }])
            ->orderBy('deadline', 'asc')->get();
    }

    /**
     * is_today_taskがtrueかつ、ログインユーザーのタスクを取得する
     *
     * @param  User  $user ユーザー
     * @return Illuminate\Database\Eloquent\Collection
     */
    public static function getTodayTasks(User $user): Collection
    {
        return Task::query()
            ->where('user_id', $user->id)
            ->where('status', 0) // 未着手
            ->where('is_today_task', true)
            ->with(['activities' => function ($query) {
                $query->orderBy('created_at', 'asc');
            }])
            ->orderBy('deadline', 'asc')->get();
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
    public static function getRecentlyCompletedTasks(User $user, int $days = 5): Collection
    {
        $date = Carbon::today()->subDays($days);

        $tasks = Task::query()
            ->where('user_id', $user->id)
            ->whereHas('activities', function ($query) use ($date) {
                $query->where('activities.created_at', '>=', $date)
                    ->where('activities.type', 5);
            })
            ->with(['activities' => function ($query) {
                $query->where('type', 5)
                    ->orderBy('created_at', 'asc');
            }])->get();

        return $tasks;
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
    public static function getRecentDeadlineTasks(User $user, int $days = 5): Collection
    {
        $date = Carbon::today()->endOfDay()->addDays($days);

        return Task::query()
            ->where('user_id', $user->id)
            ->where('status', 0) // 未着手
            ->where('deadline', '<=', $date)
            ->where('deadline', '>', Carbon::now())
            ->where('is_today_task', false)
            ->with(['activities' => function ($query) {
                $query->orderBy('created_at', 'asc');
            }])
            ->orderBy('deadline', 'asc')->get();
    }

    /**
     * deadlineが過ぎている、かつ完了していないタスクを取得する
     *
     * @param  User  $user ユーザー
     * @return Illuminate\Database\Eloquent\Collection
     */
    public static function getOverDeadlineTasks(User $user): Collection
    {
        return Task::query()
            ->where('user_id', $user->id)
            ->where('status', 0) // 未着手
            ->where('deadline', '<', Carbon::now())
            ->where('is_today_task', false)
            ->with(['activities' => function ($query) {
                $query->orderBy('created_at', 'asc');
            }])
            ->orderBy('deadline', 'asc')->get();
    }

    /**
     * タスクを今日実行するタスクに追加する
     *
     * @param    $task App\Models\Task
     */
    public static function enqueueTodayTask(Task $task): bool
    {
        // 今日実行するタスクに追加済みのタスクは追加できない
        if ($task->is_today_task) {
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
    public static function dequeueTodayTask(Task $task): bool
    {
        // 今日実行するタスクでないタスクは外せない
        if (! $task->is_today_task) {
            return false;
        }

        // 未着手のタスクのみ外せる
        if (! $task->is_today_task) {
            return false;
        }

        $task->update([
            'is_today_task' => false,
        ]);

        return true;
    }

    /**
     * すでに進行中のタスクを保留中にしてから、タスクを進行中にする
     *
     * @param    $task App\Models\Task
     */
    public static function putInProgressTask(User $user, Task $task): bool
    {
        // 今日実行するタスクのみ進行中にできる
        if (! $task->is_today_task) {
            return false;
        }

        // 進行中のタスクがあれば保留にする
        // 進行中のタスクがなければ何もしない
        $inProgressTasks = Board::getMatchedStatusTasks($user, [1]);
        if ($inProgressTasks->count() > 0) {
            $inProgressTasks->each(function ($task) {
                Board::putOnHoldTask($task);
            });
        }

        // statusを進行中にする
        $task->update([
            'status' => Task::STATUS[1]['label'],
        ]);

        return true;
    }

    /**
     * タスクを完了にする
     *
     * @param    $task App\Models\Task
     */
    public static function putCompletedTask(Task $task): bool
    {
        // 進行中のタスクのみ完了にできる
        if ($task->status['label'] !== Task::STATUS[1]['label']) {
            return false;
        }

        // statusを完了にする
        $task->update([
            'status' => Task::STATUS[3]['label'],
            'is_today_task' => false,
        ]);

        return true;
    }

    /**
     * タスクを保留にする
     *
     * @param    $task App\Models\Task
     */
    public static function putOnHoldTask(Task $task): bool
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
