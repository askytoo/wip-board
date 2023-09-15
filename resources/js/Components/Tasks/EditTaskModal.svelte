<script lang="ts">
    import { useForm } from "@inertiajs/svelte";

    import Modal from "../Modal.svelte";
    import SecondaryButton from "../SecondaryButton.svelte";
    import PrimaryButton from "../PrimaryButton.svelte";
    import LoadingSpinner from "../LoadingSpinner.svelte";
    import toast from "svelte-french-toast";
    import type { Task } from "@/types/task";

    import { editingTask } from "../../stores";
    import Form from "./Form.svelte";

    export let editing = false;
    let onClose = () => {
        $form.clearErrors();
        editing = false;
        editingTask.set({} as Task);
    };

    const form = useForm({
        title: "",
        deadline_date: "",
        deadline_time: "",
        deadline: "",
        estimated_effort: "",
        output: "",
        description: "",
        is_today_task: false,
    });

    const editTask = () => {
        $form.put(route("tasks.update", $editingTask.id), {
            preserveScroll: true,
            onBefore: () => {
                if ($editingTask.status.label === "完了") {
                    const result = confirm(
                        "完了済みのタスクを編集します。よろしいですか？"
                    );
                    result || toast.error("タスクの編集をキャンセルしました");
                    return result;
                }
            },
            onSuccess: () => {
                onClose();
                toast.success("タスクを編集しました");
            },
            onError: (errors) => {
                if (!errors)
                    toast.error(
                        "タスクの編集に失敗しました。時間を空けて再度実行してください。"
                    );
            },
        });
    };

    editingTask.subscribe((value) => {
        $form.title = value.title;
        $form.deadline_date = value.deadline?.date;
        $form.deadline_time = value.deadline?.time;
        $form.estimated_effort = value.estimated_effort;
        $form.output = value.output;
        $form.description = value.description;
        $form.is_today_task = value.is_today_task?.boolean;
    });
</script>

<Modal show={editing} {onClose}>
    <Form {form} onSubmit={editTask}>
        <div slot="title">タスク編集</div>
        <div slot="cancel-button">
            <SecondaryButton
                onClick={onClose}
                disabled={$form.processing}
                classes="w-28 justify-center"
            >
                キャンセル
            </SecondaryButton>
        </div>

        <div slot="primary-button">
            <PrimaryButton
                disabled={$form.processing}
                classes="justify-center w-28 flex gap-2"
            >
                {#if $form.processing}
                    <LoadingSpinner />
                    <div>保存中</div>
                {:else}
                    保存
                {/if}
            </PrimaryButton>
        </div>
    </Form>
</Modal>
