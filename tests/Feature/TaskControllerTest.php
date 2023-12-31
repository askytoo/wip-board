<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Carbon\Carbon;
use Inertia\Testing\AssertableInertia as Assert;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @group tasksIndex
     * @description トップページにアクセスできることを確認する
     */
    public function test_can_index(): void
    {
        $user = User::factory()->create();

        $taskNum = 5;
        $tasks = Task::factory()->count($taskNum)->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)
            ->withSession(['banned' => false])
            ->get(route('tasks.index'))
            ->assertInertia(
                fn (Assert $page) => $page
                ->has('tasks', $taskNum)
            );

    }

    /**
     * @test
     * @group tasksIndex
     * @description 他のユーザーのタスクは表示されないことを確認する
     */
    public function test_can_not_view_another_user_tasks_at_index(): void
    {
        $user = User::factory()->create();
        $anotherUser = User::factory()->create();

        $taskNum = 5;
        $tasks = Task::factory()->count($taskNum)->create([
            'user_id' => $anotherUser->id,
        ]);

        $response = $this->actingAs($user)
            ->withSession(['banned' => false])
            ->get(route('tasks.index'))
            ->assertInertia(
                fn (Assert $page) => $page
                ->has('tasks', 0)
            );
    }

    /**
     * @test
     * @group tasksStore
     * @description タスクを登録できることを確認する
     */
    public function test_can_store_task(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->withSession(['banned' => false])
            ->post(route('tasks.store'), [
                'title' => 'test title',
                'description' => 'test description',
                'deadline_date' => Carbon::today()->addDay(10)->format('Y-m-d'),
                'deadline_time' => Carbon::today()->addDay(10)->format('H:i'),
                'estimated_effort' => 1,
                'is_today_task' => true,
                'output' => 'test output',
            ]);


        $this->assertDatabaseHas('tasks', [
            'title' => 'test title',
            'description' => 'test description',
            'status' => 0,
            'deadline' => Carbon::today()->addDay(10),
            'estimated_effort' => 1,
            'is_today_task' => true,
            'output' => 'test output',
        ]);
    }

    /**
     * @test
     * @group tasksStore
     * @description 入力がない場合はタスクを登録できないことを確認する
     */
    public function test_can_not_store_task_by_empty_data(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->withSession(['banned' => false])
            ->post(route('tasks.store'), [
                'title' => '',
                'deadline_date' => '',
                'deadline_time' => '',
                'estimated_effort' => '',
                'output' => '',
            ])
            ->assertSessionHasErrors([
                'title' => 'タイトルは必ず指定してください。',
                'deadline_date' => '期限日は必ず指定してください。',
                'deadline_time' => '期限時刻は必ず指定してください。',
                'deadline' => '期日は必ず指定してください。',
                'estimated_effort' => '見積作業時間は必ず指定してください。',
                'output' => 'アウトプットは必ず指定してください。',
            ]);
    }

    /**
     * @test
     * @group tasksStore
     * @description 不正データの場合はタスクを登録できないことを確認する
     */
    public function test_can_not_store_task_by_invalid_data(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->withSession(['banned' => false])
            ->post(route('tasks.store'), [
                'title' => str_repeat('a', 256),
                'description' => str_repeat('a', 1001),
                'deadline_date' => Carbon::today()->subDay(1)->format('Y-m-d'),
                'deadline_time' => 'test deadline time',
                'estimated_effort' => 256,
                'output' => str_repeat('a', 256),
            ])
            ->assertSessionHasErrors([
                'title' => 'タイトルは、255文字以下で指定してください。',
                'description' => '詳細は、1000文字以下で指定してください。',
                'deadline_date' => '期限日には、今日以降の日付を指定してください。',
                'deadline_time' => '期限時刻はH:i形式で指定してください。',
                'deadline' => '期日には、現在より後の日付を指定してください。',
                'estimated_effort' => '見積作業時間は、0から255の間で指定してください。',
                'output' => 'アウトプットは、255文字以下で指定してください。',
            ]);
    }

    /**
     * @test
     * @group tasksDestroy
     * @description タスクを削除できることを確認する
     */
    public function test_can_destroy_task(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)
            ->withSession(['banned' => false])
            ->delete(route('tasks.destroy', $task->id));

        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
        ]);

    }

    /**
     * @test
     * @group tasksDestroy
     * @description 他のユーザーのタスクは削除できないことを確認する
     */
    public function test_can_not_destroy_another_user_task(): void
    {
        $user = User::factory()->create();
        $anotherUser = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $anotherUser->id,
        ]);

        $response = $this->actingAs($user)
            ->withSession(['banned' => false])
            ->delete(route('tasks.destroy', $task->id))
            ->assertForbidden();

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
        ]);
    }

    /**
     * @test
     * @group tasksUpdate
     * @description タスクを更新できることを確認する
     */
    public function test_can_update_task(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)
            ->withSession(['banned' => false])
            ->patch(route('tasks.update', $task->id), [
                'title' => 'test title',
                'description' => 'test description',
                'deadline_date' => Carbon::today()->addDay(10)->format('Y-m-d'),
                'deadline_time' => Carbon::today()->addDay(10)->format('H:i'),
                'estimated_effort' => 1,
                'is_today_task' => true,
                'output' => 'test output',
            ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'test title',
            'description' => 'test description',
            'status' => 0,
            'deadline' => Carbon::today()->addDay(10)->format('Y-m-d H:i:s'),
            'estimated_effort' => 1,
            'is_today_task' => true,
            'output' => 'test output',
        ]);
    }

    /**
     * @test
     * @group tasksUpdate
     * @description 他のユーザーのタスクは更新できないことを確認する
     */
    public function test_can_not_update_another_user_task(): void
    {
        $user = User::factory()->create();
        $anotherUser = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $anotherUser->id,
        ]);

        $response = $this->actingAs($user)
            ->withSession(['banned' => false])
            ->patch(route('tasks.update', $task->id), [
                'title' => 'test title',
                'description' => 'test description',
                'deadline_date' => Carbon::today()->addDay(10)->format('Y-m-d'),
                'deadline_time' => Carbon::today()->addDay(10)->format('H:i'),
                'estimated_effort' => 1,
                'is_today_task' => true,
                'output' => 'test output',
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
            'title' => 'test title',
            'description' => 'test description',
            'status' => 0,
            'deadline' => Carbon::today()->addDay(10)->format('Y-m-d H:i:s'),
            'estimated_effort' => 1,
            'is_today_task' => true,
            'output' => 'test output',
        ]);
    }

    /**
     * @test
     * @group tasksUpdate
     * @description 不正データの場合はタスクを更新できないことを確認する
     */
    public function test_can_not_update_task_by_invalid_data(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)
            ->withSession(['banned' => false])
            ->patch(route('tasks.update', $task->id), [
                'title' => str_repeat('a', 256),
                'description' => str_repeat('a', 1001),
                'deadline_date' => Carbon::today()->subDay(1)->format('Y-m-d'),
                'deadline_time' => '25:00',
                'estimated_effort' => 256,
                'output' => str_repeat('a', 256),
            ])
            ->assertSessionHasErrors([
                'title' => 'タイトルは、255文字以下で指定してください。',
                'description' => '詳細は、1000文字以下で指定してください。',
                'deadline_date' => '期限日には、今日以降の日付を指定してください。',
                'deadline_time' => '期限時刻はH:i形式で指定してください。',
                'deadline' => '期日には、現在より後の日付を指定してください。',
                'estimated_effort' => '見積作業時間は、0から255の間で指定してください。',
                'output' => 'アウトプットは、255文字以下で指定してください。',
            ]);
    }

    /**
     * @test
     * @group tasksUpdate
     * @description 入力がない場合はタスクを更新できないことを確認する
     */
    public function test_can_not_update_task_by_empty_data(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)
            ->withSession(['banned' => false])
            ->patch(route('tasks.update', $task->id), [
                'title' => '',
                'deadline_date' => '',
                'deadline_time' => '',
                'estimated_effort' => '',
                'output' => '',
            ])
            ->assertSessionHasErrors([
                'title' => 'タイトルは必ず指定してください。',
                'deadline_date' => '期限日は必ず指定してください。',
                'deadline_time' => '期限時刻は必ず指定してください。',
                'deadline' => '期日は必ず指定してください。',
                'estimated_effort' => '見積作業時間は必ず指定してください。',
                'output' => 'アウトプットは必ず指定してください。',
            ]);
    }

}
