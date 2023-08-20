<script lang="ts">
    import { page } from "@inertiajs/svelte";
    import type { Task } from "@/types/task";
    import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.svelte";
    import CreateTaskModal from "@/Components/Tasks/CreateTaskModal.svelte";
    import EditTaskModal from "@/Components/Tasks/EditTaskModal.svelte";
    import DeleteTaskModal from "@/Components/Tasks/DeleteTaskModal.svelte";

    import { editingTask } from "../../stores";
    import { deletingTask } from "../../stores";

    let tasks: Task[] = $page.props.tasks;

    let creating = false;

    let editing = false;
    const handleClickEditingButton = (task: Task) => {
        editingTask.set(task);
        editing = true;
    };

    let deleting = false;
    const handleClickDeletingButton = (task: Task) => {
        deletingTask.set(task);
        deleting = true;
    };

    $: tasks = $page.props.tasks;
</script>

<AuthenticatedLayout>
    <button on:click={() => (creating = true)} class="text-white">
        タスクを作成
    </button>
    <!-- タスク一覧のテーブル -->
    <!-- カラムはタイトル、編集ボタン、削除ボタン -->
    <div class="text-white">
        <table class="table-auto border-collapse border border-white">
            <tr>
                <th>タイトル</th>
                <th>編集</th>
                <th>削除</th>
            </tr>
            {#each tasks as task}
                <tr>
                    <td>{task.title}</td>
                    <td>
                        <button
                            on:click={() => handleClickEditingButton(task)}
                            class="text-white">編集</button
                        >
                    </td>
                    <td>
                        <button
                            on:click={() => handleClickDeletingButton(task)}
                            class="text-white">削除</button
                        >
                    </td>
                </tr>
            {/each}
        </table>
    </div>
    <CreateTaskModal bind:creating />
    <EditTaskModal bind:editing />
    <DeleteTaskModal bind:deleting />
</AuthenticatedLayout>
