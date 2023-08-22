<script lang="ts">
    import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.svelte";
    import CreateTaskModal from "@/Components/Tasks/CreateTaskModal.svelte";
    import EditTaskModal from "@/Components/Tasks/EditTaskModal.svelte";
    import DeleteTaskModal from "@/Components/Tasks/DeleteTaskModal.svelte";
    import TaskTable from "@/Components/Tasks/TaskTable.svelte";

    let creating = false;
    let editing = false;
    let deleting = false;
</script>

<AuthenticatedLayout>
    <button on:click={() => (creating = true)} class="text-white">
        タスクを作成
    </button>
    <TaskTable bind:editing bind:deleting />
    <CreateTaskModal bind:creating />
    <EditTaskModal bind:editing />
    <DeleteTaskModal bind:deleting />
</AuthenticatedLayout>
