<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use HasFactory;
    use HasUuids;

    /**
     * 代入可能な属性
     */
    protected $fillable = [
        'title',
        'description',
        'status',
        'user_id',
        'deadline',
        'started_at',
        'completed_at',
        'estimated_effort',
        'is_today_task',
        'output',
    ];

    /**
     * キャストする属性
     *
     * @var array
     */
    public const STATUS = [
        0 => ['label' => '未着手', 'class' => ''],
        1 => ['label' => '進行中', 'class' => 'green-500'],
        2 => ['label' => '保留中', 'class' => 'orange-500'],
        3 => ['label' => '完了', 'class' => 'blue-500'],
    ];

    public const IS_TODAY_TASK = [
        0 => ['label' => '', 'boolean' => false],
        1 => ['label' => '✅', 'boolean' => true],
    ];

    /**
     * statusのアクセサとミューテタの定義
     *
     * @return Illuminate\Database\Eloquent\Casts\Attribute
     */
    public static function status(): Attribute
    {
        return new Attribute(
            get: function ($value) {
                return self::STATUS[$value];
            },
            set: function ($value) {
                // labelの値からstatusの値を取得する
                $status = array_search($value, array_column(self::STATUS, 'label'));

                return $status;
            },
        );
    }

    /**
     * is_today_taskのアクセサとミューテタの定義
     *
     * @return Illuminate\Database\Eloquent\Casts\Attribute
     */
    public static function isTodayTask(): Attribute
    {
        return new Attribute(
            get: function ($value) {
                // labelの値を返す
                return self::IS_TODAY_TASK[$value];
            },
        );
    }

    protected function fomatDate(?string $value): string
    {
        if (is_null($value)) {
            return '';
        }

        $deadline = new Carbon($value);

        return $deadline->format('Y/m/d H:i');
    }

    public function deadline(): Attribute
    {
        return new Attribute(
            get: function ($value) {
                $deadline = new Carbon($value);

                return [
                    'full' => $deadline->format('Y/m/d H:i'),
                    'date' => $deadline->format('Y-m-d'),
                    'time' => $deadline->format('H:i'),
                ];
            },
        );
    }

    public function startedAt(): Attribute
    {
        return new Attribute(
            get: function ($value) {
                return $this->fomatDate($value);
            },
        );
    }

    public function completedAt(): Attribute
    {
        return new Attribute(
            get: function ($value) {
                return $this->fomatDate($value);
            },
        );
    }

    public function createdAt(): Attribute
    {
        return new Attribute(
            get: function ($value) {
                // 日本時間に変換して返す
                // 2023-08-08T23:39:24.000000Z -> 2023-08-09 08:39
                $createdAt = new Carbon($value);
                $createdAt->setTimezone('Asia/Tokyo');
                return $this->fomatDate($createdAt);
            },
        );
    }

    /**
     * リレーションの定義
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
