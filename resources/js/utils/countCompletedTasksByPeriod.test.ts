import countCompletedTasksByPeriod from "./countCompletedTasksByPeriod";
import { describe, test, expect } from "vitest";

describe("countCompletedTasksByPeriod", () => {
    test("期間内のタスクをカウントする", () => {
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
        const result = countCompletedTasksByPeriod(tasks, "2020/01/02", "2020/01/03");
        expect(result).toBe(2);
    });
});

