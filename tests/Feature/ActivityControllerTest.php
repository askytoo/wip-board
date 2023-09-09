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

        $task = Task::factory()->create([
            'user_id' => $user->id,
        ]);

        $activity = Activity::factory(2)->create([
            'task_id' => $task->id,
        ]);

        // 取得されないデータ
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
                    ->has('tasks', 1)
                    // ->has('tasks.0.activities', 2)
            );

    }
}
