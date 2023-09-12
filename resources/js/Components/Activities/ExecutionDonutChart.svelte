<script lang="ts">
    import {
        Alignments,
        ChartTheme,
        DonutChart,
        type DonutChartOptions,
    } from "@carbon/charts-svelte";
    import "@carbon/charts-svelte/styles.css";
    import type { Task } from "@/types/task";
    import getAddedTodayTaskAtFromTask from "@/utils/getAddedTodayTaskAtFromTask";
    import getCompletedAtFromTask from "@/utils/getCompletedAtFromTask";
import calculateTimeDifferenceInDays from '@/utils/calculateTimeDifferenceInDays';
    import Welcome from "@/Pages/Welcome.svelte";

    export let tasks: Task[];

    type ChartData = {
        index: number;
        group:
            | "当日"
            | "1日後"
            | "2日後"
            | "3日後"
            | "4日後"
            | "5日後"
            | "6日後"
            | "7日以上後";
        value: number;
    };

    const generateData = (tasks: Task[]): ChartData[] => {
        let data: ChartData[] = [
            { index: 0, group: "当日", value: 0 },
            { index: 1, group: "1日後", value: 0 },
            { index: 2, group: "2日後", value: 0 },
            { index: 3, group: "3日後", value: 0 },
            { index: 4, group: "4日後", value: 0 },
            { index: 5, group: "5日後", value: 0 },
            { index: 6, group: "6日後", value: 0 },
            { index: 7, group: "7日以上後", value: 0 },
        ];

        for (const task of tasks) {
            const days = calculateTimeDifferenceInDays(
                getAddedTodayTaskAtFromTask(task),
                getCompletedAtFromTask(task)
            );

            if (days >= 7) {
                data[7].value++;
            } else {
                data[days].value++;
            }
        }
        return data;
    };

    let data = generateData(tasks);

    let rate = (data[0].value / tasks.length) * 100;

    const options: DonutChartOptions = {
        title: "実行率",
        height: "400px",
        resizable: true,
        donut: {
            center: {
                numberFormatter: () => `${rate.toFixed(1)}%`,
                label: "実行率",
            },
            alignment: Alignments.CENTER,
        },
        pie: {
            sortFunction: (a, b) => a.index - b.index,
        },
        legend: {
            alignment: Alignments.CENTER,
            order: ["当日", "1日後", "2日後", "3日後", "4日後", "5日後", "6日後", "7日以上後"],
        },
        theme: ChartTheme.G90,
        animations: true,
    };
</script>

<DonutChart {data} {options} />
