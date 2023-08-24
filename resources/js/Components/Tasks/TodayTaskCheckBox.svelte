<script lang="ts">
    import Switch from "svelte-switch";
    import { router } from "@inertiajs/svelte";
    import toast from "svelte-french-toast";
    import type { Task } from "../../types/task";

    export let task: Task;
    export let isTodayTask: boolean;

    const enqueueTodayTask = (checked: boolean) => {
        router.patch(
            route("boards.enqueueTodayTask", { task: task.id }),
            {
                is_today_task: checked,
            },
            {
                preserveScroll: true,
                onSuccess: () => {
                    toast.success("今日のタスクに追加しました");
                },
                only: ["tasks", "errors"],
                onError: (errors) => {
                    console.log(errors);
                },
            }
        );
    };

    const dequeueTodayTask = (checked: boolean) => {
        router.patch(
            route("boards.dequeueTodayTask", { task: task.id }),
            {
                is_today_task: checked,
            },
            {
                preserveScroll: true,
                onSuccess: (errors) => {
                    toast.success("今日のタスクから削除しました");
                },
                only: ["tasks", "errors"],
            }
        );
    };

    function handleChange(e) {
        e.preventDefault();
        const { checked } = e.detail;
        isTodayTask = checked;
        if (checked) {
            enqueueTodayTask(checked);
        } else {
            dequeueTodayTask(checked);
        }
    }
</script>

<Switch
    id="is_today_task"
    on:change={handleChange}
    checked={isTodayTask}
    handleDiameter={false}
    disabled={task.status.label !== "未着手"}
/>
