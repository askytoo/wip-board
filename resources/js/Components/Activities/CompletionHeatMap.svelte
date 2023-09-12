<script lang="ts">
    import { HeatmapChart, HeatmapChartOptions } from "@carbon/charts-svelte";
    import type { Task } from "@/types/task";
    import getCompletedAt from "@/utils/getCompletedAtFromTask";

    export let tasks: Task[];

    type ChartData = {
        letter: string;
        week: "Sun" | "Mon" | "Tue" | "Wed" | "Thu" | "Fri" | "Sat";
        value: number;
    };

    const generateData = (tasks: Task[]): ChartData[] => {
        const data = [];
        for (let i = 0; i < tasks.length; i++) {
            const task = tasks[i];
            const completedAt = getCompletedAt(task);
            if (completedAt) {
                const date = new Date(completedAt);
                const letter = task.name;
                const week = date.toLocaleDateString("ja-JP", {
                    weekday: "short",
                }) as ChartData["week"];
                const value = 1;
                data.push({ letter, week, value });
            }
        }
        return data;
    };
</script>
