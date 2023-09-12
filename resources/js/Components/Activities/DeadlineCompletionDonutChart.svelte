<script lang="ts">
    import {
        Alignments,
        ChartTheme,
        DonutChart,
        type DonutChartOptions,
    } from "@carbon/charts-svelte";
    import "@carbon/charts-svelte/styles.css";
    import type { Task } from "@/types/task";
    import calculateHoursBetweenCompletedAtAndDeadline from "@/utils/calculateHoursBetweenCompletedAtAndDeadlinea";

    export let tasks: Task[];

    type ChartData = {
        index: number;
        group:
            | "~ -72h"
            | "-72h ~ -48h"
            | "-48h ~ -24h"
            | "-24h ~ -0h"
            | "0h ~ 24h"
            | "24h ~ 48h"
            | "48h ~ 72h"
            | "72h ~";
        value: number;
    };

    const generateData = (tasks: Task[]): ChartData[] => {
        const data: ChartData[] = [
            { index: 0, group: "~ -72h", value: 0 },
            { index: 1, group: "-72h ~ -48h", value: 0 },
            { index: 2, group: "-48h ~ -24h", value: 0 },
            { index: 3, group: "-24h ~ -0h", value: 0 },
            { index: 4, group: "0h ~ 24h", value: 0 },
            { index: 5, group: "24h ~ 48h", value: 0 },
            { index: 6, group: "48h ~ 72h", value: 0 },
            { index: 7, group: "72h ~", value: 0 },
        ];
        for (const task of tasks) {
            const hours = calculateHoursBetweenCompletedAtAndDeadline(task);
            if (hours <= -72) {
                data[0].value++;
            } else if (hours <= -48) {
                data[1].value++;
            } else if (hours <= -24) {
                data[2].value++;
            } else if (hours <= 0) {
                data[3].value++;
            } else if (hours <= 24) {
                data[4].value++;
            } else if (hours <= 48) {
                data[5].value++;
            } else if (hours <= 72) {
                data[6].value++;
            } else {
                data[7].value++;
            }
        }
        return data;
    };

    let data = generateData(tasks);

    let rate =
        ((data[0].value + data[1].value + data[2].value + data[3].value) /
            tasks.length) *
        100;

    const options: DonutChartOptions = {
        title: "対期日達成率",
        height: "400px",
        resizable: true,
        donut: {
            center: {
                numberFormatter: () => `${rate.toFixed(1)}%`,
                label: "達成率",
            },
            alignment: Alignments.CENTER,
        },
        pie: {
            sortFunction: (a, b) => a.index - b.index,
        },
        legend: {
            alignment: Alignments.CENTER,
            order: ["~ -72h", "-72h ~ -48h", "-48h ~ -24h", "-24h ~ -0h", "0h ~ 24h", "24h ~ 48h", "48h ~ 72h", "72h ~"],

        },
        theme: ChartTheme.G90,
        animations: true,
    };
</script>

<DonutChart {data} {options} />
