<script lang="ts">
    import {
        Alignments,
        ChartTheme,
        DonutChart,
        type DonutChartOptions,
    } from "@carbon/charts-svelte";
    import "@carbon/charts-svelte/styles.css";
    import type { Task } from "@/types/task";
    import getActualTaskEffort from "@/utils/getActualTaskEffort";
    import countOnHoldTaskTimes from "@/utils/countOnHoldTaskTimes";

    export let tasks: Task[];

    type ChartData = {
        index: number;
        group: "0回" | "1回" | "2回" | "3回" | "4回" | "5回 ~";
        value: number;
    };

    const generateData = (tasks: Task[]): ChartData[] => {
        let data: ChartData[] = [
            { index: 0, group: "0回", value: 0 },
            { index: 1, group: "1回", value: 0 },
            { index: 2, group: "2回", value: 0 },
            { index: 3, group: "3回", value: 0 },
            { index: 4, group: "4回", value: 0 },
            { index: 5, group: "5回 ~", value: 0 },
        ];

        for (const task of tasks) {
            const count = countOnHoldTaskTimes(task);
            if (count >= 5) {
                data[5].value++;
            } else {
                data[count].value++;
            }
        }
        return data;
    };

    let data = generateData(tasks);

    let rate = (data[0].value / tasks.length) * 100;

    const options: DonutChartOptions = {
        title: "直行率",
        height: "400px",
        resizable: true,
        donut: {
            center: {
                numberFormatter: () => `${rate.toFixed(1)}%`,
                label: "直行率"
            },
            alignment: Alignments.CENTER,
        },
        pie: {
            sortFunction: (a, b) => a.index - b.index,
        },
        legend: {
            alignment: Alignments.CENTER,
            order: ["0回", "1回", "2回", "3回", "4回", "5回 ~"],

        },
        theme: ChartTheme.G90,
        animations: true,
    };
</script>

<DonutChart {data} {options} />
