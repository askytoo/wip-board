<script lang="ts">
    import Switch from "svelte-switch";
    import axios from "axios";
    import { router } from "@inertiajs/svelte";
    import toast from "svelte-french-toast";
    import type { Task } from "../../types/task";

    export let task: Task;
    export let isTodayTask: boolean;

    const enqueueTodayTask = async () => {
        toast.promise(
            axios.patch(route("boards.enqueueTodayTask", { task: task.id }), {
                is_today_task: true,
            }),
            {
                loading: "保存中...",
                success: () => {
                    task.is_today_task.boolean = true;
                    return "今日のタスクに追加しました";
                },
                error: (errors) => {
                    console.log(errors);
                    router.reload();
                    return "サーバーエラーが発生しました。時間をおいて再度お試しください。";
                },
            }
        );
    };

    const dequeueTodayTask = () => {
        toast.promise(
            axios.patch(route("boards.dequeueTodayTask", { task: task.id }), {
                is_today_task: false,
            }),
            {
                loading: "保存中...",
                success: () => {
                    task.is_today_task.boolean = false;
                    return "今日のタスクから削除しました";
                },
                error: (errors) => {
                    console.log(errors);
                    router.reload();
                    return "サーバーエラーが発生しました。時間をおいて再度お試しください。";
                },
            }
        );
    };

    function handleChange(e) {
        e.preventDefault();
        const { checked } = e.detail;
        isTodayTask = checked;
        if (checked) {
            enqueueTodayTask();
        } else {
            dequeueTodayTask();
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
