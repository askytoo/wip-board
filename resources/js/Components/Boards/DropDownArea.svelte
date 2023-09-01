<script lang="ts">
    import { flip } from "svelte/animate";
    import { dndzone, type DndEvent } from "svelte-dnd-action";
    import convertRelativeTime from "@/utils/convertRelativeTime";
    import type { Task } from "@/types/task";
    import TextBoxEdit from "svelte-material-icons/TextBoxEdit.svelte";

    const flipDurationMs = 300;

    export const DndConsider = (e: CustomEvent<DndEvent<Task>>) => {
        tasks = e.detail.items;
    };

    export const DndFinalize = (e) => {
        tasks = e.detail.items;
    };

    export let draggingTask: Task;
    export let tasks: Task[];
    export let areaName: string;
    export let areaClasses = "";
    export let itemClasses = "";
    export let dropFromOthersDisabled = false;

    import { editingTask } from "@/stores";

    export let editing = false;

    const handleClickEditingButton = (task: Task) => {
        editingTask.set(task);
        editing = true;
    };

</script>

<div class="border-white px-3 overflow-y-auto {areaClasses}">
    <div class="text-2xl font-bold text-center text-gray-200">
        {areaName}({tasks?.length})
    </div>

    <div
        class="mx-auto h-full"
        use:dndzone={{
            items: tasks,
            centreDraggedOnCursor: true,
            flipDurationMs,
            dropFromOthersDisabled: dropFromOthersDisabled,
        }}
        on:consider={DndConsider}
        on:finalize={DndFinalize}
    >
        {#each tasks as task (task.id)}
            <div
                class="bg-gray-800 dark:bg-gray-200 text-gray-200 dark:text-gray-800 rounded-lg shadow-md p-4 my-4 mx-2"
                animate:flip={{ duration: flipDurationMs }}
                on:mousedown={() => {
                    draggingTask = task;
                }}
            >
                <div class="text-lg font-semibold mb-2">
                    {task.title}
                </div>
                <div class="pt-2 flex justify-end text-center gap-2">
                    <div class="">
                        {convertRelativeTime(task.deadline.full)}
                    </div>
                    <button on:click={() => handleClickEditingButton(task)}>
                        <TextBoxEdit
                            class="hover:text-indigo-400"
                            size={"1.5rem"}
                            title={"編集"}
                        />
                    </button>
                </div>
            </div>
        {/each}
    </div>
</div>
