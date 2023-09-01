<script lang="ts">
    import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.svelte";
    import DropDownArea from "@/Components/Boards/DropDownArea.svelte";
    import EditTaskModal from "@/Components/Tasks/EditTaskModal.svelte";
    import type { Task } from "@/types/task";

    export let overDeadlineTasks: Task[];
    export let recentDeadlineTasks: Task[];
    export let todayTasks: Task[];
    export let inProgressTask: Task;
    export let onHoldTasks: Task[];
    export let recentlyCompletedTasks: Task[];

    $: noStartedTasks = [...overDeadlineTasks, ...recentDeadlineTasks];

    let draggingItem = {} as Task;

    // 未着手は今日のタスクからのみドロップ可能
    // 今日のタスクは未着手からのみドロップ可能
    // 進行中は今日のタスクと保留中からのみドロップ可能
    // 保留中は進行中からのみドロップ可能
    // 完了は進行中からのみドロップ可能
    $: canDropNoStartedTasksAreaDisabled =
        draggingItem.status?.label === "未着手";
    $: canDropTodayTasksAreaDisabled = draggingItem.status?.label === "未着手";
    $: canDropInProgressTaskAreaDisabled =
        (draggingItem.status?.label === "未着手" &&
            draggingItem.is_today_task?.boolean === true) ||
        draggingItem.status?.label === "保留中";
    $: canDropOnHoldTaskAreaDisabled = draggingItem.status?.label === "進行中";
    $: canDropRecentlyCompletedTasksAreaDisabled =
        draggingItem.status?.label === "進行中";

    let editing = false;
</script>

<AuthenticatedLayout>
    <svelte:fragment slot="header">
        <h2
            class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
        >
            WIPボード
        </h2>
    </svelte:fragment>

    <!-- <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8"> -->
    <div class="flex h-screen">
        <div class="w-1/4">
            <DropDownArea
                areaName="未着手"
                tasks={noStartedTasks}
                areaClasses="h-full border-r"
                bind:draggingTask={draggingItem}
                dropFromOthersDisabled={!canDropNoStartedTasksAreaDisabled}
                bind:editing
            />
        </div>
        <div class="w-1/4">
            <DropDownArea
                areaName="今日のタスク"
                tasks={todayTasks}
                areaClasses="h-full border-r"
                bind:draggingTask={draggingItem}
                dropFromOthersDisabled={!canDropTodayTasksAreaDisabled}
                bind:editing
            />
        </div>
        <div class="w-1/4">
            <div class="flex-col">
                <DropDownArea
                    areaName="進行中"
                    tasks={inProgressTask}
                    areaClasses="h-80 border-b"
                    bind:draggingTask={draggingItem}
                    dropFromOthersDisabled={!canDropInProgressTaskAreaDisabled}
                    bind:editing
                />
                <DropDownArea
                    areaName="保留中"
                    tasks={onHoldTasks}
                    areaClasses="pt-10 h-full"
                    bind:draggingTask={draggingItem}
                    dropFromOthersDisabled={!canDropOnHoldTaskAreaDisabled}
                    bind:editing
                />
            </div>
        </div>
        <div class="w-1/4">
            <DropDownArea
                areaName="完了"
                tasks={recentlyCompletedTasks}
                areaClasses="h-full border-l"
                bind:draggingTask={draggingItem}
                dropFromOthersDisabled={!canDropRecentlyCompletedTasksAreaDisabled}
                bind:editing
            />
        </div>
        <!-- </div> -->
    </div>
</AuthenticatedLayout>
<EditTaskModal bind:editing />
