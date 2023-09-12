import countCompletedTasksWithinDeadline from "./countCompletedTasksWithinDeadline";
import { describe, expect, test } from "vitest";

describe("countCompletedTasksWithinDeadline", () => {
    test("期限内に完了したタスクの数を返す", () => {
        const tasks = [
            {
                id: 1,
                title: "Match Task 1",
                deadline: {
                    full: "2020/01/10 00:00",
                    date: "2020/01/10",
                    time: "00:00",
                },
                activities: [{ type: "完了", created_at: "2020/01/05 00:00" }],
            },
            {
                id: 2,
                title: "Match Task 2",
                deadline: {
                    full: "2020/01/10 00:00",
                    date: "2020/01/10",
                    time: "00:00",
                },
                activities: [{ type: "完了", created_at: "2020/01/02 00:00" }],
            },
            {
                id: 3,
                title: "Match Task 3",
                deadline: {
                    full: "2020/01/10 00:00",
                    date: "2020/01/10",
                    time: "00:00",
                },
                activities: [{ type: "完了", created_at: "2020/01/07 00:00" }],
            },
            {
                id: 4,
                title: "Unmatch Task 4",
                deadline: {
                    full: "2020/01/10 00:00",
                    date: "2020/01/10",
                    time: "00:00",
                },
                activities: [{ type: "完了", created_at: "2020/01/10 00:01" }],
            },
        ];

        expect(countCompletedTasksWithinDeadline(tasks)).toBe(3);
    });
});
