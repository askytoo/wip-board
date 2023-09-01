<script lang="ts">
    import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.svelte";
    import CreateTaskModal from "@/Components/Tasks/CreateTaskModal.svelte";
    import EditTaskModal from "@/Components/Tasks/EditTaskModal.svelte";
    import DeleteTaskModal from "@/Components/Tasks/DeleteTaskModal.svelte";
    import CopyTaskModal from "@/Components/Tasks/CopyTaskModal.svelte";
    import TaskTable from "@/Components/Tasks/TaskTable.svelte";
    import Pencil from "svelte-material-icons/Pencil.svelte";
    import PrimaryStyleButton from "@/Components/PrimaryStyleButton.svelte";

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

    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <PrimaryStyleButton
            type="button"
            onClick={() => (creating = true)}
            classes="pl-1 pr-5 ml-3 mb-3 border-none "
        >
            <Pencil class="inline-block mr-2" size="2rem" />
            <span class="text-lg"> タスクを作成 </span>
        </PrimaryStyleButton>
        <TaskTable bind:editing bind:deleting bind:copying />
    </div>
</AuthenticatedLayout>
<CreateTaskModal bind:creating />
<EditTaskModal bind:editing />
<DeleteTaskModal bind:deleting />
<CopyTaskModal bind:copying />
