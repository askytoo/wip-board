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
        2 => ['label' => '開始'],
        3 => ['label' => '保留'],
        4 => ['label' => '完了'],
        5 => ['label' => '編集'],
        6 => ['label' => '削除'],
    ];

    public static function type(): Attribute
    {
        return new Attribute(
            get: function ($value) {
                return self::TYPE[$value];
            },
            set: function ($value) {
                return array_search($value, self::TYPE);
            }
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
}
