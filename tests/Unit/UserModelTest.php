<?php

namespace Tests\Unit;

use App\Models\Activity;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @group userModel
     * @description ユーザーが作成したタスクを通してアクティビティを取得できる
     */
    public function test_can_get_activites(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);

        Activity::factory()->create([
            'task_id' => $task->id,
            'type' => 0,
        ]);

        Activity::factory()->create([
            'task_id' => $task->id,
            'type' => 1,
        ]);

        $this->assertCount(2, $user->activities()->get());
    }

}
