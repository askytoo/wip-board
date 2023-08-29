<script lang="ts">
    import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.svelte";
    import DropDownArea from "@/Components/Boards/DropDownArea.svelte";
    import type { Task } from "@/types/task";

    let noStartedItems = [
        {
            id: 1,
            title: "Aitem1",
            status: { label: "未着手" },
            is_today_task: { boolean: false },
        },
        {
            id: 2,
            title: "Aitem2",
            status: { label: "未着手" },
            is_today_task: { boolean: false },
        },
        {
            id: 3,
            title: "Aitem3",
            status: { label: "未着手" },
            is_today_task: { boolean: false },
        },
        {
            id: 4,
            title: "Aitem4",
            status: { label: "未着手" },
            is_today_task: { boolean: false },
        },
    ];

    let todayItems = [
        {
            id: 5,
            title: "Bitem1",
            status: { label: "未着手" },
            is_today_task: { boolean: true },
        },
        {
            id: 6,
            title: "Bitem2",
            status: { label: "未着手" },
            is_today_task: { boolean: true },
        },
        {
            id: 7,
            title: "Bitem3",
            status: { label: "未着手" },
            is_today_task: { boolean: true },
        },
        {
            id: 8,
            title: "Bitem4",
            status: { label: "未着手" },
            is_today_task: { boolean: true },
        },
    ];

    let inProgressItems = [
        {
            id: 9,
            title: "Citem1",
            status: { label: "進行中" },
            is_today_task: { boolean: true },
        },
    ];

    let pendingItems = [
        {
            id: 13,
            title: "Ditem1",
            status: { label: "保留中" },
            is_today_task: { boolean: true },
        },
        {
            id: 14,
            title: "Ditem2",
            status: { label: "保留中" },
            is_today_task: { boolean: true },
        },
        {
            id: 15,
            title: "Ditem3",
            status: { label: "保留中" },
            is_today_task: { boolean: true },
        },
        {
            id: 16,
            title: "Ditem4",
            status: { label: "保留中" },
            is_today_task: { boolean: true },
        },
    ];

    let completedItems = [
        {
            id: 17,
            title: "Eitem1",
            status: { label: "完了" },
            is_today_task: { boolean: false },
        },
        {
            id: 18,
            title: "Eitem2",
            status: { label: "完了" },
            is_today_task: { boolean: false },
        },
        {
            id: 19,
            title: "Eitem3",
            status: { label: "完了" },
            is_today_task: { boolean: false },
        },
        {
            id: 20,
            title: "Eitem4",
            status: { label: "完了" },
            is_today_task: { boolean: false },
        },
    ];

    let draggingItem = {} as Task;

    // 未着手は今日のタスクからのみドロップ可能
    // 今日のタスクは未着手からのみドロップ可能
    // 進行中は今日のタスクと保留中からのみドロップ可能
    // 保留中は進行中からのみドロップ可能
    // 完了は進行中からのみドロップ可能
    $: canDropNoStartedAreaDisabled = draggingItem.status?.label === "未着手";
    $: canDropTodayTasksAreaDisabled = draggingItem.status?.label === "未着手";
    $: canDropInProgressAreaDisabled =
        (draggingItem.status?.label === "未着手" &&
            draggingItem.is_today_task?.boolean === true) ||
        draggingItem.status?.label === "保留中";
    $: canDropOnHoldAreaDisabled = draggingItem.status?.label === "進行中";
    $: canDropCompletedAreaDisabled = draggingItem.status?.label === "進行中";
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
                tasks={noStartedItems}
                areaClasses="h-full border-r"
                bind:draggingItem
                dropFromOthersDisabled={!canDropNoStartedAreaDisabled}
            />
        </div>
        <div class="w-1/4">
            <DropDownArea
                areaName="今日のタスク"
                tasks={todayItems}
                areaClasses="h-full border-r"
                bind:draggingItem
                dropFromOthersDisabled={!canDropTodayTasksAreaDisabled}
            />
        </div>
        <div class="w-1/4">
            <div class="flex-col">
                <DropDownArea
                    areaName="進行中"
                    tasks={inProgressItems}
                    areaClasses="h-80 border-b"
                    bind:draggingItem
                    dropFromOthersDisabled={!canDropInProgressAreaDisabled}
                />
                <DropDownArea
                    areaName="保留中"
                    tasks={pendingItems}
                    areaClasses="pt-10 h-full"
                    bind:draggingItem
                    dropFromOthersDisabled={!canDropOnHoldAreaDisabled}
                />
            </div>
        </div>
        <div class="w-1/4">
            <DropDownArea
                areaName="完了"
                tasks={completedItems}
                areaClasses="h-full border-l"
                bind:draggingItem
                dropFromOthersDisabled={!canDropCompletedAreaDisabled}
            />
        </div>
        <!-- </div> -->
    </div></AuthenticatedLayout
>
