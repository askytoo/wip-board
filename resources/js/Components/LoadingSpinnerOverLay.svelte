<script>
    export let size = 60;
    import LoadingSpinner from "@/Components/LoadingSpinner.svelte";
</script>

<div class="overlay" />
<div class="modal">
    <LoadingSpinner {size} />
</div>

<style>
    .overlay {
        position: fixed;
        top: 0;
        left: 0;
        z-index: 1000;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
    }

    .modal {
        position: fixed;
        top: 50%;
        left: 50%;
        z-index: 1001;
        transform: translate(-50%, -50%);
    }
</style>
