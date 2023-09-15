<script lang="ts">
    import { useForm } from "@inertiajs/svelte";

    import Modal from "../Modal.svelte";
    import SecondaryButton from "../SecondaryButton.svelte";
    import PrimaryButton from "../PrimaryButton.svelte";
    import LoadingSpinner from "../LoadingSpinner.svelte";
    import toast from "svelte-french-toast";
    import getTodayDate from "../../utils/getTodayDate";
    import Form from "./Form.svelte";

    export let creating = false;
    let onClose = () => {
        $form.clearErrors();
        creating = false;
    };

    const today = getTodayDate();

    let form = useForm({
        title: "",
        deadline_date: today,
        deadline_time: new Date().toLocaleTimeString("ja-JP", {
            hour: "2-digit",
            minute: "2-digit",
        }),
        deadline: "",
        estimated_effort: 30,
        output: "",
        description: "",
        is_today_task: false,
    });

    const createTask = () => {
        $form.post(route("tasks.store"), {
            preserveScroll: true,
            onSuccess: () => {
                $form.reset();
                onClose();
                toast.success("タスクを作成しました");
            },
            onError: (errors) => {
                if (!errors)
                    toast.error(
                        "タスクの作成に失敗しました。時間を空けて再度実行してください。"
                    );
            },
        });
    };
</script>

<Modal show={creating} {onClose}>
    <Form {form} onSubmit={createTask}>
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
