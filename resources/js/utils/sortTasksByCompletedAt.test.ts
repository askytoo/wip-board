import sortTasksByCompletedAt from "./sortTasksByCompletedAt";
import { describe, expect, test } from "vitest";

describe("sortTasksByCompletedAt", () => {
    const tasks = [
        {
            id: 1,
            title: "Task 1",
            activities: [{ type: "完了", created_at: "2020/01/05 00:00:00" }],
        },
        {
            id: 2,
            title: "Task 2",
            activities: [{ type: "完了", created_at: "2020/01/02 00:00:00" }],
        },
        {
            id: 3,
            title: "Task 3",
            activities: [{ type: "完了", created_at: "2020/01/07 00:00:00" }],
        },
        {
            id: 4,
            title: "Task 4",
            activities: [{ type: "完了", created_at: "2020/01/10 00:00:00" }],
        },
    ];
    test("完了日の古い順にソートする", () => {
        const result = sortTasksByCompletedAt(tasks);

        expect(result).toEqual([
            {
                id: 2,
                title: "Task 2",
                activities: [
                    { type: "完了", created_at: "2020/01/02 00:00:00" },
                ],
            },
            {
                id: 1,
                title: "Task 1",
                activities: [
                    { type: "完了", created_at: "2020/01/05 00:00:00" },
                ],
            },
            {
                id: 3,
                title: "Task 3",
                activities: [
                    { type: "完了", created_at: "2020/01/07 00:00:00" },
                ],
            },
            {
                id: 4,
                title: "Task 4",
                activities: [
                    { type: "完了", created_at: "2020/01/10 00:00:00" },
                ],
            },
        ]);

        test("完了日の新しい順にソートする", () => {
            const result = sortTasksByCompletedAt(tasks, "desc");

            expect(result).toEqual([
                {
                    id: 4,
                    title: "Task 4",
                    activities: [
                        { type: "完了", created_at: "2020/01/10 00:00:00" },
                    ],
                },
                {
                    id: 3,
                    title: "Task 3",
                    activities: [
                        { type: "完了", created_at: "2020/01/07 00:00:00" },
                    ],
                },
                {
                    id: 1,
                    title: "Task 1",
                    activities: [
                        { type: "完了", created_at: "2020/01/05 00:00:00" },
                    ],
                },
                {
                    id: 2,
                    title: "Task 2",
                    activities: [
                        { type: "完了", created_at: "2020/01/02 00:00:00" },
                    ],
                },
            ]);
        }
    });
});
