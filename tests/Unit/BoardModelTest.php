<?php

namespace Tests\Unit;

use App\Models\Activity;
use App\Models\Board;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BoardModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     *
     * @group boardModel
     *
     * @description 指定したステータスのタスクを取得できるか確認する
     */
    public function test_can_get_single_matched_status_tasks(): void
    {
        $taskNum = 3;
        $statusNum = 0;

        $user = User::factory()->create();

        $tasks = Task::factory()->count($taskNum)->create([
            'status' => Task::STATUS[$statusNum]['label'],
            'user_id' => $user->id,
        ]);

        Activity::factory()->create([
            'task_id' => $tasks[1]->id,
            'type' => 2, // 着手
        ]);

        Activity::factory()->create([
            'task_id' => $tasks[2]->id,
            'type' => 5, // 完了
        ]);

        $notMatchedTasks = Task::factory()->count(4)->create([
            'status' => Task::STATUS[1]['label'],
            'user_id' => $user->id,
        ]);

        $matchedTasks = Board::getMatchedStatusTasks($user, [$statusNum]);

        $this->assertCount($taskNum, $matchedTasks);
        $this->assertEmpty($matchedTasks[0]->activities);
        $this->assertNotEmpty($matchedTasks[1]->activities);
        $this->assertNotEmpty($matchedTasks[2]->activities);
    }

    /**
     * @test
     *
     * @group boardModel
     *
     * @description 指定した複数のステータスのタスクを取得できるか確認する
     */
    public function test_can_get_multiple_matched_status_tasks(): void
    {
        $taskNum1 = 3;
        $statusNum1 = 0;
        $taskNum2 = 1;
        $statusNum2 = 1;

        $user = User::factory()->create();

        $tasks1 = Task::factory()->count($taskNum1)->create([
            'status' => Task::STATUS[$statusNum1]['label'],
            'user_id' => $user->id,
        ]);

        $tasks2 = Task::factory()->count($taskNum2)->create([
            'status' => Task::STATUS[$statusNum2]['label'],
            'user_id' => $user->id,
        ]);

        $matchedTasks = Board::getMatchedStatusTasks($user, [$statusNum1, $statusNum2]);

        $this->assertCount($taskNum1 + $taskNum2, $matchedTasks);
    }

    /**
     * @test
     *
     * @group boardModel
     *
     * @description 今日実行するタスクを取得できるか確認する
     */
    public function test_can_get_IsTodayTasks(): void
    {
        $taskNum = 3;

        $user = User::factory()->create();

        $tasks = Task::factory()->count($taskNum)->create([
            'is_today_task' => true,
            'user_id' => $user->id,
        ]);

        $notIsTodayTasks = Task::factory()->count(4)->create([
            'is_today_task' => false,
            'user_id' => $user->id,
        ]);

        $inProgressTask = Task::factory()->count(1)->create([
            'is_today_task' => true,
            'status' => Task::STATUS[1]['label'],
            'user_id' => $user->id,
        ]);

        $onHoldTask = Task::factory()->count(6)->create([
            'is_today_task' => true,
            'status' => Task::STATUS[2]['label'],
            'user_id' => $user->id,
        ]);

        $matchedTasks = Board::getTodayTasks($user);

        $this->assertCount($taskNum, $matchedTasks);
    }

    /**
     * @test
     *
     * @group boardModel
     *
     * @description 初期値の日数以内に完了したタスクを取得できるか確認する
     */
    public function test_can_basic_recently_completed_tasks(): void
    {
        $taskNum = 3;

        $defaultDays = 5;

        $user = User::factory()->create();

        $tasks = Task::factory()->count($taskNum)->create([
            'status' => Task::STATUS[3]['label'],
            'user_id' => $user->id,
        ]);

        // 5日前に完了したタスクのアクティビティを作成
        foreach ($tasks as $task) {
            Activity::factory()->create([
                'task_id' => $task->id,
                'type' => 5,
                'created_at' => Carbon::now()->subDay($defaultDays),
            ]);
        }

        // 取得されないアクティビティの作成( 完了のアクティビティ以外は取得されない )
        Activity::factory()->create([
            'task_id' => $tasks[1]->id,
            'type' => 2, // 着手
        ]);

        $notMatchedTasks = Task::factory()->count(4)->create([
            'status' => Task::STATUS[1]['label'],
            'user_id' => $user->id,
        ]);

        $notRecentlyCompletedTasks = Task::factory()->count(4)->create([
            'status' => Task::STATUS[3]['label'],
            'user_id' => $user->id,
        ]);

        // 6日前に完了したタスクのアクティビティを作成
        foreach ($notRecentlyCompletedTasks as $task) {
            Activity::factory()->create([
                'task_id' => $task->id,
                'type' => 5,
                'created_at' => Carbon::now()->subDay($defaultDays + 1),
            ]);
        }

        $matchedTasks = Board::getRecentlyCompletedTasks($user);

        $this->assertCount($taskNum, $matchedTasks);

        $this->assertCount(1, $matchedTasks[0]->activities); // 完了
        $this->assertCount(1, $matchedTasks[1]->activities); // 完了のみ
    }

    /**
     * @test
     *
     * @group boardModel
     *
     * @description 指定した日数以内に完了したタスクを取得できるか確認する
     */
    public function test_can_custom_recently_completed_tasks(): void
    {
        $taskNum = 3;
        $days = 4;

        $user = User::factory()->create();

        $tasks = Task::factory()->count($taskNum)->create([
            'status' => Task::STATUS[3]['label'],
            'user_id' => $user->id,
        ]);

        foreach ($tasks as $task) {
            Activity::factory()->create([
                'task_id' => $task->id,
                'type' => 5,
                'created_at' => Carbon::now()->subDay($days),
            ]);
        }

        $notRecentlyCompletedTasks = Task::factory()->count(4)->create([
            'status' => Task::STATUS[3]['label'],
            'user_id' => $user->id,
        ]);

        foreach ($notRecentlyCompletedTasks as $task) {
            Activity::factory()->create([
                'task_id' => $task->id,
                'type' => 5,
                'created_at' => Carbon::now()->subDay($days + 1),
            ]);
        }

        $matchedTasks = Board::getRecentlyCompletedTasks($user, $days);

        $this->assertCount($taskNum, $matchedTasks);
    }

    /**
     * @test
     *
     * @group boardModel
     *
     * @description 初期値の日数以内に期日が来ているタスクを取得できるか確認する
     */
    public function test_can_get_basic_recent_deadline_tasks(): void
    {
        $taskNum = 3;

        $user = User::factory()->create();

        $tasks = Task::factory()->count($taskNum)->create([
            'deadline' => Carbon::now()->addDay(5),
            'status' => Task::STATUS[0]['label'],
            'user_id' => $user->id,
        ]);

        $notRecentDeadlineTasks = Task::factory()->count(4)->create([
            'deadline' => Carbon::now()->addDay(6),
            'status' => Task::STATUS[0]['label'],
            'user_id' => $user->id,
        ]);

        $completedTasks = Task::factory()->count(4)->create([
            'deadline' => Carbon::now()->addDay(5),
            'status' => Task::STATUS[3]['label'],
            'user_id' => $user->id,
        ]);

        $matchedTasks = Board::getRecentDeadlineTasks($user);

        $this->assertCount($taskNum, $matchedTasks);
    }

    public function test_can_get_custom_recent_deadline_tasks(): void
    {
        $taskNum = 3;
        $days = 4;

        $user = User::factory()->create();

        $tasks = Task::factory()->count($taskNum)->create([
            'deadline' => Carbon::now()->addDay($days),
            'status' => Task::STATUS[0]['label'],
            'user_id' => $user->id,
        ]);

        $notRecentDeadlineTasks = Task::factory()->count(4)->create([
            'deadline' => Carbon::now()->addDay(5),
            'status' => Task::STATUS[0]['label'],
            'user_id' => $user->id,
        ]);

        $completedTasks = Task::factory()->count(4)->create([
            'deadline' => Carbon::now()->addDay($days),
            'status' => Task::STATUS[3]['label'],
            'user_id' => $user->id,
        ]);

        $matchedTasks = Board::getRecentDeadlineTasks($user, $days);

        $this->assertCount($taskNum, $matchedTasks);
    }

    public function test_can_get_over_deadline_tasks(): void
    {
        $taskNum = 3;

        $user = User::factory()->create();

        $tasks = Task::factory()->count($taskNum)->create([
            'deadline' => Carbon::now()->subDay(1),
            'status' => Task::STATUS[0]['label'],
            'user_id' => $user->id,
        ]);

        $notOverDeadlineTasks = Task::factory()->count(4)->create([
            'deadline' => Carbon::now()->addDay(1),
            'status' => Task::STATUS[0]['label'],
            'user_id' => $user->id,
        ]);

        $completedTasks = Task::factory()->count(5)->create([
            'deadline' => Carbon::now()->subDay(1),
            'status' => Task::STATUS[3]['label'],
            'user_id' => $user->id,
        ]);

        $matchedTasks = Board::getOverDeadlineTasks($user);

        $this->assertCount($taskNum, $matchedTasks);
    }

    /**
     * @test
     *
     * @description タスクを今日のタスクに追加できることを確認する
     *
     * @group boardModel
     */
    public function test_can_enqueque_today_task(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $user->id,
            'status' => Task::STATUS[0]['label'], // 未着手
            'is_today_task' => false,
        ]);

        $this->assertTrue(Board::enqueueTodayTask($task));

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'is_today_task' => true,
        ]);

    }

    /**
     * @test
     *
     * @group boardModel
     *
     * @description 今日のタスクに追加済みのタスクを今日のタスクに追加できないことを確認する
     */
    public function test_can_not_enqueue_today_if_already_today_task(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $user->id,
            'status' => Task::STATUS[0]['label'], // 未着手
            'is_today_task' => true,
        ]);

        $this->assertFalse(Board::enqueueTodayTask($task));

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'is_today_task' => true,
        ]);

    }

    /**
     * @test
     *
     * @description 未着手でないタスクを今日のタスクに追加できないことを確認する
     */
    public function test_can_not_enqueue_today_if_not_not_started(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $user->id,
            'status' => Task::STATUS[1]['label'], // 着手中
            'is_today_task' => false,
        ]);

        $this->assertFalse(Board::enqueueTodayTask($task));

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'is_today_task' => false,
        ]);

    }

    /**
     * @test
     *
     * @description タスクを今日のタスクから削除できることを確認する
     *
     * @group boardModel
     */
    public function test_can_dequeue_today_task(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $user->id,
            'status' => Task::STATUS[0]['label'], // 未着手
            'is_today_task' => true,
        ]);

        $this->assertTrue(Board::dequeueTodayTask($task));

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'is_today_task' => false,
        ]);

    }

    /**
     * @test
     *
     * @group boardModel
     *
     * @description 今日のタスクではないタスクを今日のタスクから削除できないことを確認する
     */
    public function test_can_not_dequeue_today_task_if_not_today_task(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $user->id,
            'status' => Task::STATUS[0]['label'], // 未着手
            'is_today_task' => false,
        ]);

        $this->assertFalse(Board::dequeueTodayTask($task));

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'is_today_task' => false,
        ]);

    }

    /**
     * @test
     *
     * @group boardModel
     *
     * @description 未着手でないタスクを今日のタスクから外せないことを確認する
     */
    public function test_can_not_dequeue_today_task_if_not_not_started(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $user->id,
            'status' => Task::STATUS[1]['label'], // 着手中
            'is_today_task' => false,
        ]);

        $this->assertFalse(Board::dequeueTodayTask($task));

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'is_today_task' => false,
        ]);

    }

    /**
     * @test
     *
     * @description タスクを実行中にすることができることを確認する
     *
     * @group boardModel
     */
    public function test_can_put_in_progress_task(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $user->id,
            'status' => Task::STATUS[0]['label'],
            'is_today_task' => true,
        ]);

        $this->assertTrue(Board::putInProgressTask($user, $task));

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 1,
        ]);

    }

    /**
     * @test
     *
     * @description 既に実行中のタスクがある場合、実行中のタスクをすべて保留中にしてから、実行中にすることができることを確認する
     *
     * @group boardModel
     */
    public function test_can_put_in_progress_task_when_already_exist_in_progress_task(): void
    {
        $user = User::factory()->create();
        $previousInProgressTask = Task::factory()->create([
            'title' => 'previous in progress task',
            'user_id' => $user->id,
            'status' => Task::STATUS[1]['label'], // 実行中
            'is_today_task' => true,
        ]);
        $newInProgressTask = Task::factory()->create([
            'title' => 'new in progress task',
            'user_id' => $user->id,
            'status' => Task::STATUS[0]['label'], // 未着手
            'is_today_task' => true,
        ]);

        $this->assertTrue(Board::putInProgressTask($user, $newInProgressTask));

        $this->assertDatabaseHas('tasks', [
            'title' => 'previous in progress task',
            'id' => $previousInProgressTask->id,
            'status' => 2, // 保留中
        ]);

        $this->assertDatabaseHas('tasks', [
            'title' => 'new in progress task',
            'id' => $newInProgressTask->id,
            'status' => 1, // 実行中
        ]);

    }

    /**
     * @test
     *
     * @description 今日実行するタスクに入っていないタスクを実行中にすることができないことを確認する;
     *
     * @group boardModel
     */
    public function test_can_not_put_in_progress_task_when_not_is_today_task(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $user->id,
            'status' => Task::STATUS[0]['label'],
            'is_today_task' => false,
        ]);

        $this->assertFalse(Board::putInProgressTask($user, $task));

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 0, // 未着手
        ]);

    }

    /**
     * @test
     *
     * @description タスクを完了にすることができることを確認する
     *
     * @group boardModel
     */
    public function test_can_put_completed_task(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $user->id,
            'status' => Task::STATUS[1]['label'],
        ]);

        $this->assertTrue(Board::putCompletedTask($task));

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 3, // 完了
            'is_today_task' => false,
        ]);

    }

    /**
     * @test
     *
     * @description 実行中のタスク以外のタスクを完了にすることができないことを確認する
     *
     * @group boardModel
     */
    public function test_can_not_put_completed_task_when_not_in_progress_task(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $user->id,
            'status' => Task::STATUS[0]['label'],
        ]);

        $this->assertFalse(Board::putCompletedTask($task));

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 0,
        ]);

    }

    /**
     * @test
     *
     * @description タスクを実行中から保留にすることができることを確認する
     *
     * @group boardModel
     */
    public function test_can_put_on_hold_task(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $user->id,
            'status' => Task::STATUS[1]['label'],
        ]);

        $this->assertTrue(Board::putOnHoldTask($task));

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 2,
        ]);

    }

    /**
     * @test
     *
     * @description 実行中のタスク以外のタスクを保留にすることができないことを確認する
     *
     * @group boardModel
     */
    public function test_can_not_put_on_hold_task_when_not_in_progress_task(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $user->id,
            'status' => Task::STATUS[0]['label'],
        ]);

        $this->assertFalse(Board::putOnHoldTask($task));

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 0, // 未着手
        ]);

    }
}
