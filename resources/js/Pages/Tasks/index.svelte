<script lang="ts">
    import { page } from "@inertiajs/svelte";
    import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.svelte";
    import CreateTasksModal from "@/Components/Tasks/CreateTasksModal.svelte";

    import type { Task } from "@/types/task";

    let tasks: Task[] = $page.props.tasks;

    let creating = false;

    let editing = false;
    let deleting = false;

    $: tasks = $page.props.tasks;
</script>

<AuthenticatedLayout>
    <button on:click={() => creating = true} class="text-white">タスクを作成</button>
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
                            on:click={() => (editing = true)}
                            class="text-white">編集</button
                        >
                    </td>
                    <td>
                        <button
                            on:click={() => (deleting = true)}
                            class="text-white">削除</button
                        >
                    </td>
                </tr>
            {/each}
        </table>
    </div>
    <CreateTasksModal bind:creating={creating} />
</AuthenticatedLayout>
