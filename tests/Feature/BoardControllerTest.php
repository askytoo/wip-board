<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class BoardControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $notStartedStatusNum = 0;

    protected $inProgressStatusNum = 1;

    protected $onHoldStatusNum = 2;

    protected $completedStatusNum = 3;

    /**
     * @test
     *
     * @group boardController
     *
     * @description WIPボードの表示。他のユーザーのタスクは表示されない。
     */
    public function test_can_render_index(): void
    {
        $user = User::factory()->create();

        $todayTaskNum = 3;
        $todayTasks = Task::factory()->count($todayTaskNum)->create([
            'user_id' => $user->id,
            'is_today_task' => true,
        ]);

        $onHoldTaskNum = 2;
        $onHoldTasks = Task::factory()->count($onHoldTaskNum)->create([
            'user_id' => $user->id,
            'status' => Task::STATUS[$this->onHoldStatusNum]['label'],
        ]);

        $inProgressTaskNum = 1;
        $inProgressTask = Task::factory()->count($inProgressTaskNum)->create([
            'user_id' => $user->id,
            'status' => Task::STATUS[$this->inProgressStatusNum]['label'],
        ]);

        $recentDeadlineTaskNum = 4;
        $recentDeadlineTasks = Task::factory()->count($recentDeadlineTaskNum)->create([
            'user_id' => $user->id,
            'status' => Task::STATUS[$this->notStartedStatusNum]['label'],
            'deadline' => Carbon::now()->addDay(5),
        ]);

        $recentCompletedTaskNum = 5;
        $recentCompletedTasks = Task::factory()->count($recentCompletedTaskNum)->create([
            'user_id' => $user->id,
            'status' => Task::STATUS[$this->completedStatusNum]['label'],
            'completed_at' => Carbon::now()->addDay(5),
        ]);

        $overDeadlineTaskNum = 6;
        $overDeadlineTasks = Task::factory()->count($overDeadlineTaskNum)->create([
            'user_id' => $user->id,
            'status' => Task::STATUS[$this->notStartedStatusNum]['label'],
            'deadline' => Carbon::now()->subDay(1),
        ]);

        $response = $this->actingAs($user)
            ->withSession(['banned' => false])
            ->get(route('board.index'))
            ->assertInertia(
                fn (Assert $page) => $page
                    ->has('todayTasks', $todayTaskNum)
                    ->has('onHoldTasks', $onHoldTaskNum)
                    ->has('inProgressTask', $inProgressTaskNum)
                    ->has('recentDeadlineTasks', $recentDeadlineTaskNum)
                    ->has('recentCompletedTasks', $recentCompletedTaskNum)
                    ->has('overDeadlineTasks', $overDeadlineTaskNum)
                    ->has('statuses', 4),
            );

        // 他のユーザーのタスクは表示されない
        $anotherUser = User::factory()->create();
        $response = $this->actingAs($anotherUser)
            ->withSession(['banned' => false])
            ->get(route('board.index'))
            ->assertInertia(
                fn (Assert $page) => $page
                    ->has('todayTasks', 0)
                    ->has('onHoldTasks', 0)
                    ->has('inProgressTask', 0)
                    ->has('recentDeadlineTasks', 0)
                    ->has('recentCompletedTasks', 0)
                    ->has('overDeadlineTasks', 0)
                    ->has('statuses', 4),
            );
    }

    /**
     * @test
     *
     * @group boardController
     *
     * @description 今日実行するタスクに追加する
     */
    public function test_can_enqueue_today_task(): void
    {
        $user = User::factory()->create();

        $task = Task::factory()->create([
            'user_id' => $user->id,
            'status' => Task::STATUS[$this->notStartedStatusNum]['label'],
            'is_today_task' => false,
        ]);

        $response = $this->actingAs($user)
            ->withSession(['banned' => false])
            ->patch(route('board.enqueueTodayTask', $task->id), [
                'is_today_task' => true,
            ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'is_today_task' => true,
        ]);
    }

    /**
     * @test
     *
     * @group boardController
     *
     * @description 他のユーザーのタスクは今日実行するタスクに追加できないことを確認する
     */
    public function test_can_not_enqueue_today_task_when_another_user(): void
    {
        $user = User::factory()->create();

        $task = Task::factory()->create([
            'user_id' => $user->id,
            'status' => Task::STATUS[$this->notStartedStatusNum]['label'],
            'is_today_task' => false,
        ]);

        $anotherUser = User::factory()->create();

        $response = $this->actingAs($anotherUser)
            ->withSession(['banned' => false])
            ->patch(route('board.enqueueTodayTask', $task->id), [
                'is_today_task' => true,
            ])->assertStatus(403);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'is_today_task' => false,
        ]);
    }

    /**
     * @test
     *
     * @group boardController
     *
     * @description 今日実行するタスクから削除する
     */
    public function test_can_dequeue_today_task(): void
    {
        $user = User::factory()->create();

        $task = Task::factory()->create([
            'user_id' => $user->id,
            'status' => Task::STATUS[$this->notStartedStatusNum]['label'],
            'is_today_task' => true,
        ]);

        $response = $this->actingAs($user)
            ->withSession(['banned' => false])
            ->patch(route('board.dequeueTodayTask', $task->id), [
                'is_today_task' => false,
            ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'is_today_task' => false,
        ]);
    }

    /**
     * @test
     *
     * @group boardController
     *
     * @description 他のユーザーのタスクは今日実行するタスクから削除できないことを確認する
     */
    public function test_can_not_dequeue_today_task_when_another_user(): void
    {
        $user = User::factory()->create();

        $task = Task::factory()->create([
            'user_id' => $user->id,
            'status' => Task::STATUS[$this->notStartedStatusNum]['label'],
            'is_today_task' => true,
        ]);

        $anotherUser = User::factory()->create();

        $response = $this->actingAs($anotherUser)
            ->withSession(['banned' => false])
            ->patch(route('board.dequeueTodayTask', $task->id), [
                'is_today_task' => false,
            ])->assertStatus(403);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'is_today_task' => true,
        ]);
    }

    /**
     * @test
     *
     * @group boardController
     *
     * @description タスクを進行中にすることができることを確認する
     */
    public function test_can_put_in_progress_task(): void
    {
        $user = User::factory()->create();

        $task = Task::factory()->create([
            'user_id' => $user->id,
            'is_today_task' => true,
            'status' => Task::STATUS[$this->notStartedStatusNum]['label'],
        ]);

        $response = $this->actingAs($user)
            ->withSession(['banned' => false])
            ->patch(route('board.putInProgressTask', $task->id), [
                'status' => Task::STATUS[$this->inProgressStatusNum]['label'],
            ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => $this->inProgressStatusNum,
        ]);
    }

    /**
     * @test
     *
     * @group boardController
     *
     * @description 他のユーザーのタスクは進行中にできないことを確認する
     */
    public function test_can_not_put_in_progress_task_when_another_user(): void
    {
        $user = User::factory()->create();

        $task = Task::factory()->create([
            'user_id' => $user->id,
            'is_today_task' => true,
            'status' => Task::STATUS[$this->notStartedStatusNum]['label'],
        ]);

        $anotherUser = User::factory()->create();

        $response = $this->actingAs($anotherUser)
            ->withSession(['banned' => false])
            ->patch(route('board.putInProgressTask', $task->id), [
                'status' => Task::STATUS[$this->inProgressStatusNum]['label'],
            ])->assertStatus(403);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => $this->notStartedStatusNum,
        ]);
    }

    /**
     * @test
     *
     * @group boardController
     *
     * @description 他のユーザーのタスクは保留中にできないことを確認する
     */
    public function test_can_not_put_on_hold_task_when_another_user(): void
    {
        $user = User::factory()->create();

        $task = Task::factory()->create([
            'user_id' => $user->id,
            'is_today_task' => true,
            'status' => Task::STATUS[$this->inProgressStatusNum]['label'],
        ]);

        $anotherUser = User::factory()->create();

        $response = $this->actingAs($anotherUser)
            ->withSession(['banned' => false])
            ->patch(route('board.putOnHoldTask', $task->id), [
                'status' => Task::STATUS[$this->onHoldStatusNum]['label'],
            ])
            ->assertStatus(403);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => $this->inProgressStatusNum,
        ]);
    }

    /**
     * @test
     *
     * @group boardController
     *
     * @description 進行中のタスクを保留中にできることを確認する
     */
    public function test_can_put_on_hold_task(): void
    {
        $user = User::factory()->create();

        $task = Task::factory()->create([
            'user_id' => $user->id,
            'is_today_task' => true,
            'status' => Task::STATUS[$this->inProgressStatusNum]['label'],
        ]);

        $response = $this->actingAs($user)
            ->withSession(['banned' => false])
            ->patch(route('board.putOnHoldTask', $task->id), [
                'status' => Task::STATUS[$this->onHoldStatusNum]['label'],
            ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => $this->onHoldStatusNum,
        ]);
    }

    /**
     * @test
     *
     * @group boardController
     *
     * @description タスクを完了にすることができることを確認する
     */
    public function test_can_put_completed_task(): void
    {
        $user = User::factory()->create();

        $task = Task::factory()->create([
            'user_id' => $user->id,
            'status' => Task::STATUS[$this->inProgressStatusNum]['label'],
        ]);

        $response = $this->actingAs($user)
            ->withSession(['banned' => false])
            ->patch(route('board.putCompletedTask', $task->id), [
                'status' => Task::STATUS[$this->completedStatusNum]['label'],
            ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => $this->completedStatusNum,
        ]);
    }

    /**
     * @test
     *
     * @group boardController
     *
     * @description 他のユーザーのタスクは完了にできないことを確認する
     */
    public function test_can_not_put_completed_task_when_another_user(): void
    {
        $user = User::factory()->create();

        $task = Task::factory()->create([
            'user_id' => $user->id,
            'status' => Task::STATUS[$this->inProgressStatusNum]['label'],
        ]);

        $anotherUser = User::factory()->create();

        $response = $this->actingAs($anotherUser)
            ->withSession(['banned' => false])
            ->patch(route('board.putCompletedTask', $task->id), [
                'status' => Task::STATUS[$this->completedStatusNum]['label'],
            ])
            ->assertStatus(403);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => $this->inProgressStatusNum,
        ]);
    }
}
