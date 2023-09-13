<script lang="ts">
    import CalHeatmap from "cal-heatmap";
    import Legend from "cal-heatmap/plugins/Legend";
    import LegendLite from "cal-heatmap/plugins/LegendLite";
    import Tooltip from "cal-heatmap/plugins/Tooltip";
    import CalendarLabel from "cal-heatmap/plugins/CalendarLabel";
    import "cal-heatmap/cal-heatmap.css";
    import type { Task } from "@/types/task";
    import getCompletedAt from "@/utils/getCompletedAtFromTask";
    export let tasks: Task[];

    type ChartData = {
        date: string;
        value: number;
    };

    const generateData = (tasks: Task[]): ChartData[] => {
        const data: ChartData[] = [];
        for (const task of tasks) {
            const completedDate = new Date(getCompletedAt(task)).toDateString();
            const index = data.findIndex((d) => d.date === completedDate);
            if (index === -1) {
                data.push({ date: completedDate, value: 1 });
            } else {
                data[index].value += 1;
            }
        }
        return data;
    };

    const data = generateData(tasks);

    const today = new Date();
    const tenMonthsAgo = new Date(
        today.getFullYear(),
        today.getMonth() - 10,
        today.getDate()
    );

    const cal: CalHeatmap = new CalHeatmap();
    cal.paint(
        {
            data: {
                source: data,
                x: "date",
                y: "value",
            },
            date: { start: tenMonthsAgo, timezone: "Asia/Tokyo" },
            range: 14,
            scale: {
                color: {
                    type: "threshold",
                    scheme: "purples",
                    domain: [0, 2, 4, 6, 8, 10],
                },
            },
            domain: {
                type: "month",
                gutter: 4,
                label: { text: "MMM", textAlign: "middle", position: "top" },
            },
            subDomain: {
                type: "day",
                radius: 2,
                width: 16,
                height: 16,
                gutter: 4,
            },
            itemSelector: "#ex-ghDay",
        },
        [
            [
                Tooltip,
                {
                    text: function (date, value, dayjsDate) {
                        return (
                            (value ? value : "No") +
                            " Completion tasks " +
                            dayjsDate.format("YYYY/MM/DD")
                        );
                    },
                },
            ],
            [
                LegendLite,
                {
                    includeBlank: true,
                    itemSelector: "#ex-ghDay-legend",
                    radius: 2,
                    width: 11,
                    height: 11,
                    gutter: 4,
                },
            ],
            [
                CalendarLabel,
                {
                    width: 30,
                    textAlign: "start",
                    text: () =>
                        dayjs
                            .weekdaysShort()
                            .map((d, i) => (i % 2 == 0 ? "" : d)),
                    padding: [25, 0, 0, 0],
                },
            ],
        ]
    );
</script>

<div class="text-gray-800 dark:text-gray-200 rounded-lg pt-4 overflow-hidden">
    <div id="ex-ghDay" class="mb-3" />
    <button
        class="pt-2 px-2 transition-colors ease-in-out hover:text-indigo-400 disabled:text-gray-600"
        on:click={(e) => {
            e.preventDefault();
            cal.previous();
        }}
    >
        ← Previous
    </button>
    <button
        class="pt-2 px-2 transition-colors ease-in-out hover:text-indigo-400 disabled:text-gray-600"
        on:click={(e) => {
            e.preventDefault();
            cal.next();
        }}
    >
        Next →
    </button>
    <div style="float: right; fontSize: 12">
        <span style="color: #768390">Less</span>
        <div
            id="ex-ghDay-legend"
            style="display: inline-block; margin: 0 4px"
        />
        <span style="color: #768390; fontSize: 12">More</span>
    </div>
</div>
