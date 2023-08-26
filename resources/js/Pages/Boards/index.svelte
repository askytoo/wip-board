<script lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.svelte";

</script>

<AuthenticatedLayout>
    <svelte:fragment slot="header">
        <h2
            class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
        >
            WIPボード
        </h2>
    </svelte:fragment>

    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    </div>
</AuthenticatedLayout>

