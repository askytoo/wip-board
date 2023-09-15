<script lang="ts">
    import TextInput from "../TextInput.svelte";
    import InputLabel from "../InputLabel.svelte";
    import InputError from "../InputError.svelte";
    import Switch from "svelte-switch";
    import SveltyPicker, { config } from "svelty-picker";
    import { jp } from "svelty-picker/i18n";

    export let form;
    export let onSubmit = () => {};

    function handleChange(e: CustomEvent) {
        const { checked } = e.detail;
        $form.is_today_task = checked;
    }
</script>

<form on:submit|preventDefault={onSubmit} class="p-6">
    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
        <slot name="title" />
    </h2>

    <div class="mt-6">
        <InputLabel for="title" value="タイトル" classes="" required />

        <TextInput
            id="title"
            bind:value={$form.title}
            type="text"
            classes="mt-1 block w-3/4"
            required
            autofocus
            disabled={$form.processing}
        />

        <InputError message={$form.errors.title} />
    </div>

    <div class="mt-4">
        <InputLabel for="deadline" value="期日" classes="" required />

        <div class="flex">
            <InputLabel for="deadline_date" value="期日日" classes="sr-only" />

            <SveltyPicker
                id="deadline_date"
                mode="date"
                manualInput={true}
                format="yyyy-mm-dd"
                displayFormat="yyyy/mm/dd"
                bind:value={$form.deadline_date}
                inputClasses="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm mt-1 mr-4"
                clearBtn={false}
                todayBtn={false}
                required
                disabled={$form.processing}
                i18n={jp}
            />

            <InputLabel
                for="deadline_time"
                value="期日時刻"
                classes="sr-only"
            />

            <SveltyPicker
                id="deadline_time"
                mode="time"
                manualInput={true}
                format="hh:ii"
                displayFormat="hh:ii"
                bind:value={$form.deadline_time}
                inputClasses="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm mt-1"
                clearBtn={false}
                required
                disabled={$form.processing}
                i18n={jp}
            />
        </div>
        <InputError message={$form.errors.deadline} />
    </div>

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
            disabled={$form.processing}
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
            disabled={$form.processing}
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
            disabled={$form.processing}
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
            disabled={$form.processing}
        />
    </div>
    <InputError message={$form.errors.is_today_task} />

    <div class="mt-6 flex justify-end gap-3">
        <!-- キャンセルボタンとPrimaryボタンは親要素で定義する -->
        <slot name="cancel-button" />
        <slot name="primary-button" />
        <div class="mt-6 flex justify-end" />
    </div>
</form>
