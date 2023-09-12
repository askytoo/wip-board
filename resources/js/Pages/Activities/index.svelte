<script lang="ts">
    import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.svelte";
    import type { Task } from "@/types/task";
    import filterTasksByCompletedAt from "@/utils/filterTasksByCompletedAt";
    import sortTasksByCompletedAt from "@/utils/sortTasksByCompletedAt";
    import TaskAchievementBarChart from "@/Components/Activities/TaskAchievementBarChart.svelte";
    import DeadlineCompletionDonutChart from "@/Components/Activities/DeadlineCompletionDonutChart.svelte";
    import EstimateVsActualEffortDonutChart from "@/Components/Activities/EstimateVsActualEffortDonutChart.svelte";
    import DirectCompletionDonutChart from "@/Components/Activities/DirectCompletionDonutChart.svelte";
    import ExecutionDonutChart from "@/Components/Activities/ExecutionDonutChart.svelte";
    export let completedTasks: Task[];

    // デフォルトの日付は今日の7日前のYYYY/MM/DDに設定
    const startDate = new Date(
        new Date().setDate(new Date().getDate() - 7)
    ).toLocaleDateString("ja-JP", {
        year: "numeric",
        month: "2-digit",
        day: "2-digit",
    });

    // デフォルトの日付は今日のYYYY/MM/DD
    let endDate = new Date().toLocaleDateString("ja-JP", {
        year: "numeric",
        month: "2-digit",
        day: "2-digit",
    });

    let filteredTasks: Task[];
    $: filteredTasks = filterTasksByCompletedAt(
        completedTasks,
        startDate,
        endDate
    );

    let sortedTasks: Task[];
    $: sortedTasks = sortTasksByCompletedAt(filteredTasks);
</script>

<AuthenticatedLayout>
    <svelte:fragment slot="header">
        <h2
            class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
        >
            アクティビティ
        </h2>
    </svelte:fragment>

    <div
        class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 h-full pb-48 overflow-auto hidden-scrollbar"
    >
        <div class="md:columns-2 sm:columns-1 pt-4">
            <div class="column pr-3">
                <DeadlineCompletionDonutChart tasks={sortedTasks} />
            </div>
            <div class="column pl-3">
                <EstimateVsActualEffortDonutChart tasks={sortedTasks} />
            </div>
        </div>
        <div class="md:columns-2 sm:columns-1 pt-8">
            <div class="column pr-3">
                <DirectCompletionDonutChart tasks={sortedTasks} />
            </div>
            <div class="column pl-3">
                <ExecutionDonutChart tasks={sortedTasks} />
            </div>
        </div>

        <TaskAchievementBarChart
            tasks={sortedTasks}
            {startDate}
            {endDate}
            days={1}
        />
    </div>
</AuthenticatedLayout>
