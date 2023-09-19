<script lang="ts">
    import { flip } from "svelte/animate";
    import { dndzone, type DndEvent } from "svelte-dnd-action";
    import convertRelativeTime from "@/utils/convertRelativeTime";
    import type { Task } from "@/types/task";
    import TextBoxEdit from "svelte-material-icons/TextBoxEdit.svelte";
    import Delete from "svelte-material-icons/Delete.svelte";
    import ContentCopy from "svelte-material-icons/ContentCopy.svelte";

    export let draggingTask: Task;
    export let tasks: Task[];
    export let areaName: string;
    export let dropFromOthersDisabled = false;
    export let onDrop: (task: Task) => void;

    const flipDurationMs = 300;

    // 進行中のエリアにドロップされた場合は、
    // ドロップされたタスク以外を保留中のエリアに移動させる
    import { onHoldAreaTasks } from "@/stores";
    const onDropToInProgressArea = (tasks: Task[]) => {
        const draggingTaskId = draggingTask.id;
        const _newTasks = tasks.filter(
            (task) =>
                task.id !== draggingTaskId &&
                task.id !== "id:dnd-shadow-placeholder-0000"
        );
        const newTasks = _newTasks.map((task) => {
            return {
                ...task,
                status: {
                    ...task.status,
                    label: "保留中",
                },
            };
        });
        console.log("before", $onHoldAreaTasks);
        onHoldAreaTasks.set([...newTasks, ...$onHoldAreaTasks.filter((task) => task.id !== draggingTaskId)]);
        console.log("after", $onHoldAreaTasks);
    };

    // onDragを実行するためのフラグ
    let isFromOthers = true;
    let isEnter = false;

    const dndConsider = (e: CustomEvent<DndEvent<Task>>) => {
        // ドラッグしているタスクが自分のエリアからの場合
        if (e.detail.info.trigger === "dragStarted") {
            isFromOthers = false;
        }

        // ドラッグしているタスクが自分のエリアに入った場合
        if (e.detail.info.trigger === "draggedEntered") {
            isEnter = true;
        }

        // ドラッグしているタスクが自分のエリアから出た場合
        if (e.detail.info.trigger === "draggedLeft") {
            isEnter = false;
        }

        tasks = e.detail.items;
    };

    const dndFinalize = (e: CustomEvent<DndEvent<Task>>) => {
        let updated = false;
        // ドラッグしているタスクが他のエリアのもので、自分のエリアに入った場合にonDropを実行する
        if (
            isFromOthers &&
            isEnter &&
            e.detail.info.trigger === "droppedIntoZone"
        ) {
            // dbに保存する処理
            onDrop(draggingTask);

            // ドロップされたエリアが進行中のエリアの場合は、
            // ドロップされたタスク以外を保留中のエリアに移動させる
            if (areaName === "進行中") {
                onDropToInProgressArea(tasks);
                tasks = [draggingTask];
                updated = true;
            }
        }

        if (updated === false) {
            tasks = e.detail.items;
        }
        // フラグの初期化
        isFromOthers = true;
        isEnter = false;
    };

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

    import getStartedAt from "@/utils/getStartedAtFromTask";
    import getCompletedAt from "@/utils/getCompletedAtFromTask";
</script>

<div class="border-white px-3 h-full pb-12">
    <div
        class="text-2xl font-bold text-center text-gray-800 dark:text-gray-200 pb-4"
    >
        {areaName}({tasks?.length})
    </div>

    <div
        class="mx-auto h-full overflow-y-auto hidden-scrollbar border border-gray-700 shadow-xl shadow-gray-950 rounded-lg py-2"
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
                                {convertRelativeTime(getCompletedAt(task))}完了
                            {:else if task.status.label === "進行中"}
                                {convertRelativeTime(getStartedAt(task))}開始
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
