<script lang="ts">
    import { useForm } from "@inertiajs/svelte";

    import Modal from "../Modal.svelte";
    import SecondaryButton from "../SecondaryButton.svelte";
    import PrimaryButton from "../PrimaryButton.svelte";
    import LoadingSpinner from "../LoadingSpinner.svelte";
    import toast from "svelte-french-toast";
    import Form from "./Form.svelte";
    import type { Task } from "@/types/task";

    import { copyingTask } from "../../stores";

    export let copying = false;
    let onClose = () => {
        $form.clearErrors();
        copying = false;
        copyingTask.set({} as Task);
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

    const copyTask = () => {
        $form.post(route("tasks.store"), {
            preserveScroll: true,
            onSuccess: () => {
                $form.reset();
                onClose();
                toast.success("タスクを作成しました");
            },
            onError: (errors) => {
            if (!errors)
                toast.error("タスクの作成に失敗しました。時間を空けて再度実行してください。");
            },
        });
    };

    copyingTask.subscribe((value) => {
        $form.title = value.title;
        $form.deadline_date = value.deadline?.date;
        $form.deadline_time = value.deadline?.time;
        $form.estimated_effort = value.estimated_effort;
        $form.output = value.output;
        $form.description = value.description;
        $form.is_today_task = value.is_today_task;
    });

</script>

<Modal show={copying} {onClose}>
    <Form {form} onSubmit={copyTask}>
        <div slot="title">タスク作成</div>
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
            ;
            <PrimaryButton
                disabled={$form.processing}
                classes="justify-center w-28 flex gap-2"
            >
                {#if $form.processing}
                    <LoadingSpinner />
                    <div>作成中</div>
                {:else}
                    作成
                {/if}
            </PrimaryButton>
        </div>
    </Form>
</Modal>
