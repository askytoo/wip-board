<script lang="ts">
    import { page } from "@inertiajs/svelte";
    import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.svelte";
    import CreateTasksModal from "@/Components/Tasks/CreateTasksModal.svelte";

    import type { Task } from "@/types/task";

    let tasks: Task[] = $page.props.tasks;

    let creating = true;
</script>

<AuthenticatedLayout>
    {#each tasks as task, index}
        <ui>
            <li>
                {task.is_today_task.label}
            </li>
        </ui>
    {/each}
    <CreateTasksModal {creating} />
</AuthenticatedLayout>
