<script lang="ts">
    import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.svelte";
    import CreateTaskModal from "@/Components/Tasks/CreateTaskModal.svelte";
    import EditTaskModal from "@/Components/Tasks/EditTaskModal.svelte";
    import DeleteTaskModal from "@/Components/Tasks/DeleteTaskModal.svelte";
    import CopyTaskModal from "@/Components/Tasks/CopyTaskModal.svelte";
    import TaskTable from "@/Components/Tasks/TaskTable.svelte";

    let creating = false;
    let editing = false;
    let deleting = false;
    let copying = false;
</script>

<AuthenticatedLayout>
    <svelte:fragment slot="header">
        <h2
            class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
        >
            タスク一覧
        </h2>
    </svelte:fragment>

    <button on:click={() => (creating = true)} class="text-white">
        タスクを作成
    </button>
    <TaskTable bind:editing bind:deleting bind:copying />
</AuthenticatedLayout>
<CreateTaskModal bind:creating />
<EditTaskModal bind:editing />
<DeleteTaskModal bind:deleting />
<CopyTaskModal bind:copying />
