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

    export let tasks: Task[];

    type ChartData = {
        index: number;
        group:
            | "~ 50%"
            | "50% ~ 60%"
            | "60% ~ 70%"
            | "70% ~ 80%"
            | "80% ~ 90%"
            | "90% ~ 100%"
            | "100% ~ 110%"
            | "110% ~ 120%"
            | "120% ~ 130%"
            | "130% ~ 140%"
            | "140% ~ 150%"
            | "150% ~";
        value: number;
    };

    const generateData = (tasks: Task[]): ChartData[] => {
        const data: ChartData[] = [
            { index: 0, group: "~ 50%", value: 0 },
            { index: 1, group: "50% ~ 60%", value: 0 },
            { index: 2, group: "60% ~ 70%", value: 0 },
            { index: 3, group: "70% ~ 80%", value: 0 },
            { index: 4, group: "80% ~ 90%", value: 0 },
            { index: 5, group: "90% ~ 100%", value: 0 },
            { index: 6, group: "100% ~ 110%", value: 0 },
            { index: 7, group: "110% ~ 120%", value: 0 },
            { index: 8, group: "120% ~ 130%", value: 0 },
            { index: 9, group: "130% ~ 140%", value: 0 },
            { index: 10, group: "140% ~ 150%", value: 0 },
            { index: 11, group: "150% ~", value: 0 },
        ];

        for (const task of tasks) {
            const rate =
                (getActualTaskEffort(task) / task.estimated_effort) * 100;
            if (rate <= 50) {
                data[0].value++;
            } else if (rate <= 60) {
                data[1].value++;
            } else if (rate <= 70) {
                data[2].value++;
            } else if (rate <= 80) {
                data[3].value++;
            } else if (rate <= 90) {
                data[4].value++;
            } else if (rate <= 100) {
                data[5].value++;
            } else if (rate <= 110) {
                data[6].value++;
            } else if (rate <= 120) {
                data[7].value++;
            } else if (rate <= 130) {
                data[8].value++;
            } else if (rate <= 140) {
                data[9].value++;
            } else if (rate <= 150) {
                data[10].value++;
            } else {
                data[11].value++;
            }
        }
        return data;
    };

    let data = generateData(tasks);

    let rate =
        ((data[0].value +
            data[1].value +
            data[2].value +
            data[3].value +
            data[4].value +
            data[5].value) /
            tasks.length) *
        100;

    const options: DonutChartOptions = {
        title: "対見積作業時間達成率",
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
            order: [
                "~ 50%",
                "50% ~ 60%",
                "60% ~ 70%",
                "70% ~ 80%",
                "80% ~ 90%",
                "90% ~ 100%",
                "100% ~ 110%",
                "110% ~ 120%",
                "120% ~ 130%",
                "130% ~ 140%",
                "140% ~ 150%",
                "150% ~",
            ],
        },
        theme: ChartTheme.G90,
        animations: true,
    };
</script>

<DonutChart {data} {options} />
