<script lang="ts">
    import { useForm } from "@inertiajs/svelte";

    import Modal from "../Modal.svelte";
    import TextInput from "../TextInput.svelte";
    import InputLabel from "../InputLabel.svelte";
    import InputError from "../InputError.svelte";
    import SecondaryButton from "../SecondaryButton.svelte";
    import PrimaryButton from "../PrimaryButton.svelte";
    import LoadingSpinner from "../LoadingSpinner.svelte";
    import Switch from "svelte-switch";
    import toast from "svelte-french-toast";
    import OptionTime from "../OptionTime.svelte";
    import getTodayDate from "../../utils/getTodayDate";
    import roundUpTime from "../../utils/roundUpTime";

    export let creating = false;
    let onClose = () => {
        creating = false;
    };

    const today = getTodayDate();

    const step = 900;
    const now = roundUpTime(step, new Date());

    const form = useForm({
        title: "",
        deadline_date: today,
        deadline_time: now,
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
            only: ["tasks"],
        });
    };

    function handleChange(e) {
        const { checked } = e.detail;
        $form.is_today_task = checked;
    }
</script>

<Modal show={creating} {onClose}>
    <form on:submit|preventDefault={createTask} class="p-6">
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            タスク作成
        </h2>

        <div class="mt-6">
            <InputLabel for="title" value="タイトル" classes="" required />

            <TextInput
                id="title"
                bind:value={$form.title}
                type="text"
                classes="mt-1 block w-3/4"
                required
                isFocused
            />

            <InputError message={$form.errors.title} />
        </div>

        <div class="mt-4">
            <InputLabel for="deadline" value="期日" classes="" required />

            <div class="flex">
                <InputLabel
                    for="deadline_date"
                    value="期日日"
                    classes="sr-only"
                />

                <TextInput
                    id="deadline_date"
                    bind:value={$form.deadline_date}
                    type="date"
                    classes="mt-1 block"
                    required
                />

                <div class="w-4" />
                <InputLabel
                    for="deadline_time"
                    value="期日時刻"
                    classes="sr-only"
                />

                <select
                    id="deadline_time"
                    bind:value={$form.deadline_time}
                    class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm mt-1 block w-full"
                    on:change={(e) => {}}
                    required
                >
                    <OptionTime selectedOption={now} {step} />
                </select>
            </div>
        </div>
        <InputError message={$form.errors.deadline} />

        <div class="mt-4">
            <InputLabel
                for="estimated_effort"
                value="見積作業時間(分)"
                classes=""
                required
            />

            <TextInput
                id="estimated_effort"
                bind:value={$form.estimated_effort}
                type="text"
                classes="mt-1 block w-3/4"
                required
            />

            <InputError message={$form.errors.estimated_effort} />
        </div>

        <div class="mt-4">
            <InputLabel
                for="estimated_effort"
                value="アウトプット"
                classes=""
                required
            />

            <TextInput
                id="output"
                bind:value={$form.output}
                type="text"
                classes="mt-1 block w-3/4"
                required
            />

            <InputError message={$form.errors.output} />
        </div>

        <div class="mt-4">
            <InputLabel for="description" value="詳細" classes="" />

            <TextInput
                id="description"
                bind:value={$form.description}
                type="text"
                classes="mt-1 block w-3/4 h-20"
            />

            <InputError message={$form.errors.description} />
        </div>

        <div class="mt-4 flex gap-3 items-center">
            <InputLabel
                for="is_today_task"
                value="今日のタスクに追加する"
                classes=""
            />

            <Switch
                id="is_today_task"
                on:change={handleChange}
                checked={$form.is_today_task}
                handleDiameter={false}
            />
        </div>
        <InputError message={$form.errors.is_today_task} />

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
                    <div>作成中</div>
                {:else}
                    作成
                {/if}
            </PrimaryButton>
            <div class="mt-6 flex justify-end" />
        </div>
    </form>
</Modal>
