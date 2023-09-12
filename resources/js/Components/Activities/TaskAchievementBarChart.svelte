<script lang="ts">
    import {
        BarChartSimple,
        type BarChartOptions,
        ChartTheme,
        ScaleTypes,
    } from "@carbon/charts-svelte";
    import "@carbon/charts-svelte/styles.css";
    import type { Task } from "@/types/task";
    import generatePeriods from "@/utils/generatePeriods";
    import countCompletedTasksByperiod from "@/utils/countCompletedTasksByPeriod";
    export let tasks: Task[];
    export let startDate: string;
    export let endDate: string;
    export let days: number;

    type ChartData = {
        group: string;
        value: number;
    };

    const generateData = (
        tasks: Task[],
        startDate: string,
        endDate: string,
        days: number
    ): ChartData[] => {
        const data = [];

        const periods = generatePeriods(startDate, endDate, days);
        for (const period of periods) {
            data.push({
                group: period.startDate + "~",
                value: countCompletedTasksByperiod(
                    tasks,
                    period.startDate,
                    period.endDate
                ),
            });
        }
        return data;
    };

    let data = generateData(tasks, startDate, endDate, days);

    let options: BarChartOptions = {
        title: "タスク達成数",
        axes: {
            left: {
                mapsTo: "value",
            },
            bottom: {
                mapsTo: "group",
                scaleType: ScaleTypes.LABELS,
            },
        },
        height: "400px",
        theme: ChartTheme.G90,
        animations: true,
    };
</script>

<BarChartSimple {data} {options} />
