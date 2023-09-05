<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Activity extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'user_id',
        'task_id',
        'type',
    ];

    public const TYPE = [
        0 => ['label' => '作成'],
        1 => ['label' => '今日のタスクに追加'],
        2 => ['label' => '今日のタスクから削除'],
        3 => ['label' => '開始'],
        4 => ['label' => '保留'],
        5 => ['label' => '完了'],
        6 => ['label' => '編集'],
    ];

    public static function type(): Attribute
    {
        return new Attribute(
            get: function ($value) {
                return self::TYPE[$value]['label'];
            },
        );
    }

    /**
     * リレーションの定義
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<User>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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
    public static function recordIsTodayTask(User $user, Task $task, bool $previousIsTodayTask = false): void
    {
        if (! $previousIsTodayTask && $task->is_today_task['boolean']) {
            // 今日実行するタスクに追加された場合
            Activity::create([
                'user_id' => $user->id,
                'task_id' => $task->id,
                'type' => 1,
            ]);
        } elseif ($previousIsTodayTask && ! $task->is_today_task['boolean']) {
            // 今日実行するタスクから削除された場合
            Activity::create([
                'user_id' => $user->id,
                'task_id' => $task->id,
                'type' => 2,
            ]);
        }
    }
}
