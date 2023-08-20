<script lang="ts">
    import { useForm } from "@inertiajs/svelte";

    import Modal from "../Modal.svelte";
    import TextInput from "../TextInput.svelte";
    import InputLabel from "../InputLabel.svelte";
    import InputError from "../InputError.svelte";
    import SecondaryButton from "../SecondaryButton.svelte";
    import PrimaryButton from "../PrimaryButton.svelte";
    import LoadingSpinner from "../LoadingSpinner.svelte";
    import toast from "svelte-french-toast";
    import type { Task } from "@/types/task";

    import { deletingTask } from "../../stores";

    export let deleting = false;
    let onClose = () => {
        $form.reset();
        deleting = false;
        deletingTask.set({} as Task);
    };

    let errorMessage: string | null = "";

    const form = useForm({
        title: "",
    });

    const deleteTask = () => {
        if (!validate()) {
            return;
        }

        $form.delete(route("tasks.destroy", $deletingTask.id), {
            preserveScroll: true,
            onSuccess: () => {
                onClose();
                toast.success("タスクを削除しました");
            },
            only: ["tasks"],
        });
    };

    const validate = () => {
        if ($form.title !== $deletingTask.title) {
            errorMessage = "入力値が正しくありません。";
            return false;
        } else {
            errorMessage= "";
            return true;
        }
    };
</script>

<Modal show={deleting} {onClose}>
    <form on:submit|preventDefault={deleteTask} class="p-6">
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            タスク削除
        </h2>
        <div class="mt-4 text-sm text-gray-500 dark:text-gray-400">
            <p>タスクを削除します。この操作は取り消せません。</p>
            <p>
                タスクを削除する場合は、入力欄に
                <span class="text-red-500 dark:text-red-400">
                    {" "}
                    {$deletingTask?.title}{" "}
                </span>
                と入力してください。
            </p>
        </div>

        <div class="mt-6">
            <InputLabel for="title" value="タイトル" classes="sr-only" />

            <TextInput
                id="title"
                bind:value={$form.title}
                type="text"
                classes="mt-1 block w-3/4"
                required
            />

            <InputError message={errorMessage} />
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <SecondaryButton
                onClick={onClose}
                disabled={$form.processing}
                classes="w-28 justify-center"
            >
                キャンセル
            </SecondaryButton>

            <PrimaryButton
                disabled={$form.processing}
                classes="justify-center w-28 flex gap-2"
            >
                {#if $form.processing}
                    <LoadingSpinner />
                    <div>削除中</div>
                {:else}
                    削除
                {/if}
            </PrimaryButton>
            <div class="mt-6 flex justify-end" />
        </div>
    </form>
</Modal>
