<script lang="ts">
    import { flip } from "svelte/animate";
    import { dndzone, type DndEvent } from "svelte-dnd-action";
    import convertRelativeTime from "@/utils/convertRelativeTime";
    import type { Task } from "@/types/task";
    import TextBoxEdit from "svelte-material-icons/TextBoxEdit.svelte";
    import Delete from "svelte-material-icons/Delete.svelte";
    import ContentCopy from "svelte-material-icons/ContentCopy.svelte";

    const flipDurationMs = 300;

    export const dndConsider = (e: CustomEvent<DndEvent<Task>>) => {
        tasks = e.detail.items;
    };

    // const dndFinalizeInProgressTask = (e: CustomEvent<DndEvent<Task>>) => {
    //     // ドロップされたタスクが増えた場合のみonDropを実行する
    //     if (
    //         e.detail.info.trigger === "droppedIntoZone" &&
    //         previousTasksNumber < e.detail.items.length
    //     ) {
    //         if (previousTasksNumber > 0) {
    //             const answer: boolean = confirm(
    //                 "進行中にできるタスクは1つだけです。他のタスクを保留中に移動しますか？"
    //             );
    //
    //             if (!answer) {
    //                 toast.error("進行中に移動するのをキャンセルしました");
    //                 return;
    //             }
    //         }
    //
    //         onDrop(draggingTask);
    //     }
    // };

    export const dndFinalize = (e: CustomEvent<DndEvent<Task>>) => {
        // ドロップされたタスクが増えた場合のみonDropを実行する
        if (
            e.detail.info.trigger === "droppedIntoZone" &&
            previousTasksNumber < e.detail.items.length
        ) {
            onDrop(draggingTask);
        }
        tasks = e.detail.items;
    };

    export let draggingTask: Task;
    export let tasks: Task[];
    export let previousTasksNumber: number;
    export let areaName: string;
    export let dropFromOthersDisabled = false;
    export let onDrop: (task: Task) => void;

    import { editingTask } from "@/stores";

    export let editing = false;

    const handleClickEditingButton = (task: Task) => {
        editingTask.set(task);
        editing = true;
    };

    import { deletingTask } from "../../stores";

    export let deleting = false;

    const handleClickDeletingButton = (task: Task) => {
        deletingTask.set(task);
        deleting = true;
    };

    import { copyingTask } from "../../stores";

    export let copying = false;

    const handleClickCopyingButton = (task: Task) => {
        copyingTask.set(task);
        copying = true;
    };

    const getTaskCardColor = (task: Task) => {
        if (task.status.label === "完了") {
            return "bg-blue-500";
        }

        const deadline = task.deadline.full;
        const now = new Date();
        const deadlineDate = new Date(deadline);
        const diff = deadlineDate.getTime() - now.getTime();

        if (diff < 0) {
            return "bg-red-500";
        } else if (diff <= 3 * 24 * 60 * 60 * 1000) {
            return "bg-yellow-500";
        } else if (diff <= 5 * 24 * 60 * 60 * 1000) {
            return "bg-green-500";
        } else {
            return "bg-gray-500";
        }
    };
</script>

<div class="border-white px-3 h-full pb-12">
    <div class="text-2xl font-bold text-center text-gray-200 pb-4">
        {areaName}({tasks?.length})
    </div>

    <div
        class="mx-auto h-full overflow-y-auto hidden-scrollbar"
        style="min-height: 10rem;"
        use:dndzone={{
            items: tasks,
            centreDraggedOnCursor: true,
            flipDurationMs,
            dropFromOthersDisabled: dropFromOthersDisabled,
        }}
        on:consider={dndConsider}
        on:finalize={dndFinalize}
    >
        {#each tasks as task (task.id)}
            <div
                class="bg-gray-800 dark:bg-gray-200 text-gray-200 dark:text-gray-800 rounded-lg shadow-md mb-4 mx-2"
                animate:flip={{ duration: flipDurationMs }}
                on:pointerdown={() => {
                    draggingTask = task;
                }}
            >
                <div
                    class="rounded-t-lg h-2.5 w-full {getTaskCardColor(task)}"
                />
                <div class="p-4 pt-2">
                    <div class="text-lg font-semibold mb-2">
                        {task.title}
                    </div>
                    <div class="pt-2 flex justify-end text-center">
                        <div class="pr-4">
                            {#if task.status.label === "完了"}
                                {convertRelativeTime(task.completed_at)}完了
                            {:else if task.status.label === "進行中"}
                                {convertRelativeTime(task.started_at)}開始
                            {:else}
                                期日: {convertRelativeTime(task.deadline.full)}
                            {/if}
                        </div>
                        <button on:click={() => handleClickEditingButton(task)}>
                            <TextBoxEdit
                                class="hover:text-indigo-400"
                                size={"1.5rem"}
                                title={"編集"}
                            />
                        </button>
                        <button
                            on:click={() => handleClickDeletingButton(task)}
                            class="ml-1"
                        >
                            <Delete
                                class="hover:text-indigo-400"
                                size={"1.5rem"}
                                title={"削除"}
                            />
                        </button>
                        <button
                            on:click={() => handleClickCopyingButton(task)}
                            class="ml-1"
                        >
                            <ContentCopy
                                class="hover:text-indigo-400"
                                size={"1.5rem"}
                                title={"複製"}
                            />
                        </button>
                    </div>
                </div>
            </div>
        {/each}
    </div>
</div>
