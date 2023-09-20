<script lang="ts">
    import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.svelte";
    import DropDownArea from "@/Components/Boards/DropDownArea.svelte";
    import CreateTaskModal from "@/Components/Tasks/CreateTaskModal.svelte";
    import EditTaskModal from "@/Components/Tasks/EditTaskModal.svelte";
    import DeleteTaskModal from "@/Components/Tasks/DeleteTaskModal.svelte";
    import CopyTaskModal from "@/Components/Tasks/CopyTaskModal.svelte";
    import Pencil from "svelte-material-icons/Pencil.svelte";
    import PrimaryStyleButton from "@/Components/PrimaryStyleButton.svelte";
    import type { Task } from "@/types/task";
    import { onHoldAreaTasks } from "@/stores";

    export let overDeadlineTasks: Task[];
    export let recentDeadlineTasks: Task[];
    export let todayTasks: Task[];
    export let inProgressTask: Task[];
    export let onHoldTasks: Task[];
    export let recentlyCompletedTasks: Task[];

    $: noStartedTasks = [...overDeadlineTasks, ...recentDeadlineTasks];

    // 完了したタスクは完了日時でソートする
    $: sortedRecentlyCompletedTasks = recentlyCompletedTasks.sort((a, b) => {
        if (a.activities[0].created_at < b.activities[0].created_at) {
            return 1;
        } else {
            return -1;
        }
    });

    // 進行中のタスクを必ず1つにするために
    // 保留中のタスクは、storeに保存する
    $: onHoldAreaTasks.set(onHoldTasks);

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
            draggingItem.is_today_task === true) ||
        draggingItem.status?.label === "保留中";
    $: canDropOnHoldTaskAreaDisabled = draggingItem.status?.label === "進行中";
    $: canDropRecentlyCompletedTasksAreaDisabled =
        draggingItem.status?.label === "進行中";

    let creating = false;
    let editing = false;
    let deleting = false;
    let copying = false;

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
    import LoadingSpinnerOverLay from "@/Components/LoadingSpinnerOverLay.svelte";
    import axios from "axios";
    let isProcessing = false;

    const enqueueTodayTask =  (task: Task): Task => {
        let newTask = {...task};
        toast.promise(
            axios.patch(route("boards.enqueueTodayTask", { task: task.id }), {
                is_today_task: true,
            }),
            {
                loading: "保存中...",
                success: () => {
                    newTask.is_today_task = true;
                    return "今日のタスクに追加しました";
                },
                error: (errors) => {
                    console.log(errors);
                    router.reload();
                    return "サーバーエラーが発生しました。時間をおいて再度お試しください。";
                },
            }
        );

        return newTask;
    };

    const dequeueTodayTask = (task: Task): Task => {
        let newTask = {...task};
        toast.promise(
            axios.patch(route("boards.dequeueTodayTask", { task: task.id }), {
                is_today_task: false,
            }),
            {
                loading: "保存中...",
                success: () => {
                    newTask.is_today_task = false;
                    return "今日のタスクから削除しました";
                },
                error: (errors) => {
                    console.log(errors);
                    router.reload();
                    return "サーバーエラーが発生しました。時間をおいて再度お試しください。";
                },
            }
        );
        return newTask;
    };

    // 進行中のタスクは1つだけにする
    // 進行中のタスクがすでにある場合は、他のタスクを保留中に移動する
    const putInProgressTask = (task: Task) => {
        let newTask = {...task};
        toast.promise(
            axios.patch(route("boards.putInProgressTask", { task: task.id }), {
                status: "進行中",
            }),
            {
                loading: "保存中...",
                success: () => {
                    newTask.status.label = "進行中";
                    return "進行中に移動しました";
                },
                error: (errors) => {
                    console.log(errors);
                    router.reload();
                    return "サーバーエラーが発生しました。時間をおいて再度お試しください。";
                },
            }
        );
        return newTask;
    };

    const putOnHoldTask = (task: Task) => {
        let newTask = {...task};
        toast.promise(
            axios.patch(route("boards.putOnHoldTask", { task: task.id }), {
                status: "保留中",
            }),
            {
                loading: "保存中...",
                success: () => {
                    newTask.status.label = "保留中";
                    return "保留中に移動しました";
                },
                error: (errors) => {
                    console.log(errors);
                    router.reload();
                    return "サーバーエラーが発生しました。時間をおいて再度お試しください。";
                },
            }
        );
        return newTask;
    };

    const putCompletedTask = (task: Task) => {
        let newTask = {...task};
        toast.promise(
            axios.patch(route("boards.putCompletedTask", { task: task.id }), {
                status: "完了",
            }),
            {
                loading: "保存中...",
                success: () => {
                    newTask.status.label = "完了";
                    return "完了に移動しました";
                },
                error: (errors) => {
                    console.log(errors);
                    router.reload();
                    return "サーバーエラーが発生しました。時間をおいて再度お試しください。";
                },
            }
        );
        return newTask;
    };
</script>

<AuthenticatedLayout>
    {#if isProcessing}
        <LoadingSpinnerOverLay />
    {/if}
    <svelte:fragment slot="header">
        <h2
            class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
        >
            WIPボード
        </h2>
    </svelte:fragment>

    <!-- <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8"> -->
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 h-full pb-[13rem]">
        <PrimaryStyleButton
            type="button"
            onClick={() => (creating = true)}
            classes="pl-1 pr-5 ml-3 mb-3 border-none "
        >
            <Pencil class="inline-block mr-2" size="2rem" />
            <span class="text-lg"> タスクを作成 </span>
        </PrimaryStyleButton>
        <div class="flex h-full">
            <div class="w-1/4 h-full">
                <DropDownArea
                    areaName="未着手"
                    tasks={noStartedTasks}
                    bind:draggingTask={draggingItem}
                    dropFromOthersDisabled={!canDropNoStartedTasksAreaDisabled}
                    bind:editing
                    bind:deleting
                    bind:copying
                    onDrop={dequeueTodayTask}
                />
            </div>
            <div class="w-1/4 h-full">
                <DropDownArea
                    areaName="今日のタスク"
                    tasks={todayTasks}
                    bind:draggingTask={draggingItem}
                    dropFromOthersDisabled={!canDropTodayTasksAreaDisabled}
                    bind:editing
                    bind:deleting
                    bind:copying
                    onDrop={enqueueTodayTask}
                />
            </div>
            <div class="w-1/4 flex-col h-full">
                <div bind:this={upperContainer} class="min-h-80 pb-5">
                    <DropDownArea
                        areaName="進行中"
                        tasks={inProgressTask}
                        bind:draggingTask={draggingItem}
                        dropFromOthersDisabled={!canDropInProgressTaskAreaDisabled}
                        bind:editing
                        bind:deleting
                        bind:copying
                        onDrop={putInProgressTask}
                    />
                </div>
                <div bind:this={lowerContainer} class="h-full pb-18">
                    <DropDownArea
                        areaName="保留中"
                        tasks={$onHoldAreaTasks}
                        bind:draggingTask={draggingItem}
                        dropFromOthersDisabled={!canDropOnHoldTaskAreaDisabled}
                        bind:editing
                        bind:deleting
                        bind:copying
                        onDrop={putOnHoldTask}
                    />
                </div>
            </div>
            <div class="w-1/4 h-full">
                <DropDownArea
                    areaName="完了"
                    tasks={sortedRecentlyCompletedTasks}
                    bind:draggingTask={draggingItem}
                    dropFromOthersDisabled={!canDropRecentlyCompletedTasksAreaDisabled}
                    bind:editing
                    bind:deleting
                    bind:copying
                    onDrop={putCompletedTask}
                />
            </div>
            <!-- </div> -->
        </div>
    </div>
</AuthenticatedLayout>
<CreateTaskModal bind:creating />
<EditTaskModal bind:editing />
<DeleteTaskModal bind:deleting />
<CopyTaskModal bind:copying />
