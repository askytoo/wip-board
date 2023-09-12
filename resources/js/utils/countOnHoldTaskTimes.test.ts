import countOnHoldTaskTimes from "./countOnHoldTaskTimes";
import { describe, expect, test } from "vitest";

describe("countOnHoldTaskTimes", () => {
    test("タスクの保留回数を返す", () => {
        const task = {
            id: 1,
            title: "タスク1",
            activities: [
                {
                    id: 1,
                    task_id: 1,
                    type: "着手",
                    created_at: "2021-09-01 10:00",
                },
                {
                    id: 2,
                    task_id: 1,
                    type: "保留",
                    created_at: "2021-09-01 12:00",
                },
                {
                    id: 3,
                    task_id: 1,
                    type: "再開",
                    created_at: "2021-09-01 13:00",
                },
                {
                    id: 4,
                    task_id: 1,
                    type: "保留",
                    created_at: "2021-09-01 14:00",
                },
                {
                    id: 5,
                    task_id: 1,
                    type: "再開",
                    created_at: "2021-09-01 15:00",
                },
                {
                    id: 6,
                    task_id: 1,
                    type: "完了",
                    created_at: "2021-09-01 16:00",
                },
            ],
        };
        expect(countOnHoldTaskTimes(task)).toBe(2);
    });
});
