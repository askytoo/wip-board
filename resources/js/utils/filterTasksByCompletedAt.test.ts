import { describe, expect, test } from "vitest";
import filterTasksByCompletedAt from "./filterTasksByCompletedAt";

describe("filterTasksByCompletedAt", () => {
    test("startDateとendDateの間に完了日があるタスクを返す", () => {
        const tasks = [
            {
                id: 1,
                title: "Task 1",
                activities: [
                    { type: "完了", created_at: "2020/01/01 00:00:00" }
                ]
            },
            {
                id: 2,
                title: "Task 2",
                activities: [

                    { type: "完了", created_at: "2020/01/02 00:00:00" }
                ]
            },
            {
                id: 3,
                title: "Task 3",
                activities: [
                    { type: "完了", created_at: "2020/01/03 00:00:00" }
                ]
            },
            {
                id: 4,
                title: "Task 4",
                activities: [
                    { type: "完了", created_at: "2020/01/04 00:00:00" }
                ]
            },
        ];

        const startDate = "2020/01/02";
        const endDate = "2020/01/03";
        const result = filterTasksByCompletedAt(tasks, startDate, endDate);

        expect(result).toEqual([
            {
                id: 2,
                title: "Task 2",
                activities: [
                    { type: "完了", created_at: "2020/01/02 00:00:00" }
                ]
            },
            {
                id: 3,
                title: "Task 3",
                activities: [
                    { type: "完了", created_at: "2020/01/03 00:00:00" }
                ]
            },
        ]);
    });
