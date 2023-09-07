<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Activity extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'task_id',
        'type',
    ];

    public const TYPE = [
        0 => ['label' => '今日のタスクに追加'],
        1 => ['label' => '今日のタスクから削除'],
        2 => ['label' => '着手'],
        3 => ['label' => '再開'],
        4 => ['label' => '保留'],
        5 => ['label' => '完了'],
    ];

    public static function type(): Attribute
    {
        return new Attribute(
            get: function ($value) {
                return self::TYPE[$value]['label'];
            },
        );
    }

    public function createdAt(): Attribute
    {
        return new Attribute(
            // アクセサ
            get: fn ($value) => Carbon::parse($value)
                ->timezone('Asia/Tokyo')
                ->format('Y-m-d H:i'),
        );
    }

    /**
     * リレーションの定義
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOneThrough<User>
     */
    public function user(): HasOneThrough
    {
        return $this->hasOneThrough(User::class, Task::class);
    }

    /**
     * リレーションの定義
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Task>
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * タスクを今日実行するタスクに追加または削除したときのアクティビティを記録
     *
     * @param  User  $user ユーザー
     * @param  Task  $task タスク
     * @param  bool  $previousIsTodayTask データが更新される前のis_today_taskの値。新規作成の場合は引数なし。
     */
    public static function recordIsTodayTask(Task $task, bool $previousIsTodayTask = false): void
    {
        if (! $previousIsTodayTask && $task->is_today_task['boolean']) {
            // 今日実行するタスクに追加された場合
            Activity::create([
                'task_id' => $task->id,
                'type' => 0,
            ]);
        } elseif ($previousIsTodayTask && ! $task->is_today_task['boolean']) {
            // 今日実行するタスクから削除された場合
            Activity::create([
                'task_id' => $task->id,
                'type' => 1,
            ]);
        }
    }

    /**
     * タスクを実行中に更新したときのアクティビティを記録
     *
     * @param  Task  $previousTask 更新前のタスク
     */
    public static function recordInProgressTask(Task $previousTask): void
    {
        if ($previousTask->status['label'] === Task::STATUS[0]['label']) {
            // 更新前のタスクが未着手だった場合は着手のアクティビティを記録
            Activity::create([
                'task_id' => $previousTask->id,
                'type' => 2,
            ]);
        } elseif ($previousTask->status['label'] === Task::STATUS[2]['label']) {
            // 更新前のタスクが保留だった場合は再開のアクティビティを記録
            Activity::create([
                'task_id' => $previousTask->id,
                'type' => 3,
            ]);
        }

    }
}
