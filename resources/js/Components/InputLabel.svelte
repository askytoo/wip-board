<script lang="ts">
    export let value: string | null = null,
        classes: string,
        required = false;
</script>

<div class="flex gap-1">
    <label
        class="block font-medium text-sm text-gray-700 dark:text-gray-300 {classes}"
        {...$$props}
    >
        {#if value}
            <span>{value}</span>
        {:else}
            <span><slot /></span>
        {/if}
    </label>
    {#if required}
        <div class="text-red-500">*</div>
    {/if}
</div>
