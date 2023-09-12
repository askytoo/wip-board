<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ActivityControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     *
     * @group ActivityController
     *
     * @description トップページにアクセスできることを確認する
     */
    public function test_can_index(): void
    {

        $user = User::factory()->create();

        $completedTaskNum = 3;
        $completedTasks = Task::factory($completedTaskNum)->create([
            'user_id' => $user->id,
            'status' => 3, // 完了
        ]);

        foreach ($completedTasks as $completedTask) {
            Activity::factory()->create([
                'task_id' => $completedTask->id,
                'type' => 1, // 今日実行するタスクに追加
            ]);

            Activity::factory()->create([
                'task_id' => $completedTask->id,
                'type' => 3, // 着手
            ]);

            Activity::factory()->create([
                'task_id' => $completedTask->id,
                'type' => 5, // 完了
            ]);
        }

        // 取得されないデータ
        $notCompletedTask = Task::factory()->create([
            'user_id' => $user->id,
            'status' => 0, // 未着手
        ]);

        $notCompletedActivity = Activity::factory()->create([
            'task_id' => $notCompletedTask->id,
            'type' => '1', // 今日実行するタスクに追加
        ]);

        // 他のユーザーのデータ
        $anotherUser = User::factory()->create();

        $anotherTask = Task::factory()->create([
            'user_id' => $anotherUser->id,
        ]);

        $anotherActivity = Activity::factory(1)->create([
            'task_id' => $anotherTask->id,
        ]);

        $response = $this->actingAs($user)
            ->withSession(['banned' => false])
            ->get(route('activities.index'))
        ->assertInertia(
            fn (Assert $page) => $page
                ->has('completedTasks', 3)
                ->has('completedTasks.0.activities', 3)
        );

    }
}
