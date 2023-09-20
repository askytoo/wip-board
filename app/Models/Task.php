<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        0 => ['label' => '未着手'],
        1 => ['label' => '進行中'],
        2 => ['label' => '保留中'],
        3 => ['label' => '完了'],
    ];

    public const IS_TODAY_TASK = [
        0 => false,
        1 => true
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
     * is_today_taskのアクセサの定義
     *
     * @return Illuminate\Database\Eloquent\Casts\Attribute
     */
    public static function isTodayTask(): Attribute
    {
        return new Attribute(
            get: function ($value) {
                return self::IS_TODAY_TASK[$value];
            },
        );
    }

    protected function fomatDate(?string $value): string
    {
        if (is_null($value)) {
            return '';
        }

        $date = new Carbon($value);

        return $date->format('Y/m/d H:i');
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

    public function createdAt(): Attribute
    {
        return new Attribute(
            get: fn ($value) => Carbon::parse($value)
                ->timezone('Asia/Tokyo')
                ->format('Y-m-d h:i')
        );
    }

    /**
     * リレーションの定義
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo<User>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * リレーションの定義
     *
     * @return Illuminate\Database\Eloquent\Relations\HasMany<Activity>
     */
    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }
}
