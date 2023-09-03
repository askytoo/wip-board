<script lang="ts">
    import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.svelte";
    import DropDownArea from "@/Components/Boards/DropDownArea.svelte";
    import EditTaskModal from "@/Components/Tasks/EditTaskModal.svelte";
    import type { Task } from "@/types/task";

    export let overDeadlineTasks: Task[];
    export let recentDeadlineTasks: Task[];
    export let todayTasks: Task[];
    export let inProgressTask: Task[];
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

    let upperContainer: HTMLDivElement;
    let lowerContainer: HTMLDivElement;

    // upperContainerの高さを取得して、lowerContainerの高さを設定する
    // upperContainerとlowerContainerをあわせた高さを親要素の高さに合わせる
    $: {
        if (upperContainer && lowerContainer) {
            const upperContainerHeight = upperContainer.offsetHeight;
            lowerContainer.style.height = `calc(100% - ${upperContainerHeight}px)`;
        }
    }

    import { router } from "@inertiajs/svelte";
    import toast from "svelte-french-toast";
    const enqueueTodayTask = (task: Task) => {
        router.patch(
            route("boards.enqueueTodayTask", { task: task.id }),
            { is_today_task: true },
            {
                preserveScroll: true,
                onSuccess: () => {
                    toast.success("今日のタスクに追加しました");
                },
            }
        );
    };

    const dequeueTodayTask = (task: Task) => {
        router.patch(
            route("boards.dequeueTodayTask", { task: task.id }),
            { is_today_task: false },
            {
                preserveScroll: true,
                onSuccess: () => {
                    toast.success("今日のタスクから削除しました");
                },
            }
        );
    };

    // 進行中のタスクは1つだけにする
    // 進行中のタスクがすでにある場合は、他のタスクを保留中に移動する
    const putInProgressTask = (task: Task) => {
        router.patch(
            route("boards.putInProgressTask", { task: task.id }),
            { status: "進行中" },
            {
                preserveScroll: true,
                onSuccess: () => {
                    toast.success("進行中に移動しました");
                },
            }
        );
    };

    const putOnHoldTask = (task: Task) => {
        router.patch(
            route("boards.putOnHoldTask", { task: task.id }),
            { status: "保留中" },
            {
                preserveScroll: true,
                onSuccess: () => {
                    toast.success("保留中に移動しました");
                },
            }
        );
    };

    const putCompletedTask = (task: Task) => {
        router.patch(
            route("boards.putCompletedTask", { task: task.id }),
            { status: "完了" },
            {
                preserveScroll: true,
                onSuccess: () => {
                    toast.success("完了に移動しました");
                },
            }
        );
    };
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
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 h-full pb-36">
        <div class="flex h-full">
            <div class="w-1/4 border-l border-r h-full">
                <DropDownArea
                    areaName="未着手"
                    tasks={noStartedTasks}
                    bind:draggingTask={draggingItem}
                    dropFromOthersDisabled={!canDropNoStartedTasksAreaDisabled}
                    bind:editing
                    onDrop={dequeueTodayTask}
                    previousTasksNumber={noStartedTasks.length}
                />
            </div>
            <div class="w-1/4 border-r">
                <DropDownArea
                    areaName="今日のタスク"
                    tasks={todayTasks}
                    bind:draggingTask={draggingItem}
                    dropFromOthersDisabled={!canDropTodayTasksAreaDisabled}
                    bind:editing
                    onDrop={enqueueTodayTask}
                    previousTasksNumber={todayTasks.length}
                />
            </div>
            <div class="w-1/4 flex-col h-full">
                <div bind:this={upperContainer} class="min-h-80 border-b pb-5">
                    <DropDownArea
                        areaName="進行中"
                        tasks={inProgressTask}
                        bind:draggingTask={draggingItem}
                        dropFromOthersDisabled={!canDropInProgressTaskAreaDisabled}
                        bind:editing
                        onDrop={putInProgressTask}
                        previousTasksNumber={inProgressTask.length}
                    />
                </div>
                <div bind:this={lowerContainer} class="pt-10 h-full">
                    <DropDownArea
                        areaName="保留中"
                        tasks={onHoldTasks}
                        bind:draggingTask={draggingItem}
                        dropFromOthersDisabled={!canDropOnHoldTaskAreaDisabled}
                        bind:editing
                        onDrop={putOnHoldTask}
                        previousTasksNumber={onHoldTasks.length}
                    />
                </div>
            </div>
            <div class="w-1/4 border-l border-r">
                <DropDownArea
                    areaName="完了"
                    tasks={recentlyCompletedTasks}
                    bind:draggingTask={draggingItem}
                    dropFromOthersDisabled={!canDropRecentlyCompletedTasksAreaDisabled}
                    bind:editing
                    onDrop={putCompletedTask}
                    previousTasksNumber={recentlyCompletedTasks.length}
                />
            </div>
            <!-- </div> -->
        </div>
    </div>
</AuthenticatedLayout>
<EditTaskModal bind:editing />
