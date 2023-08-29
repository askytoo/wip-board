<script lang="ts">
    import { flip } from "svelte/animate";
    import { dndzone, type DndEvent } from "svelte-dnd-action";
    import type { Task } from "@/types/task";

    const flipDurationMs = 300;

    export const DndConsider = (e: CustomEvent<DndEvent<Task>>) => {
        tasks = e.detail.items;
    };

    export const DndFinalize = (e) => {
        tasks = e.detail.items;
    };

    export let draggingItem: Task;
    export let tasks: Task[];
    export let areaName: string;
    export let areaClasses = "";
    export let itemClasses = "";
    export let dropFromOthersDisabled = false;
</script>

<div class="border-white px-3 overflow-y-auto {areaClasses}">
    <div class="text-2xl font-bold text-center text-gray-200">
        {areaName}({tasks.length})
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
                class="my-2 {draggingItem.id === task.id ? 'bg-red-500' : ''}}"
                animate:flip={{ duration: flipDurationMs }}
                on:mousedown={() => {
                    draggingItem = task;
                }}
            >
                <div
                    class="mx-auto border boder-white text-white {itemClasses}"
                >
                    {task.title}
                </div>
            </div>
        {/each}
    </div>
</div>
