import calculateDeviationPercentageByTaskEstimateAndActualEffort from "./calculateDeviationPercentageByEstimateAndActualEffort";
import { describe, expect, test } from "vitest";

describe("calculateDeviationPercentageByTaskEstimateAndActualEffort", () => {
    test("タスクの見積りと実績工数の偏差率を返す", () => {
        const task = {
            id: 1,
            title: "タスク1",
            estimated_effort: 60,
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
                    type: "完了",
                    created_at: "2021-09-01 12:00",
                },
            ],
        };
        expect(
            calculateDeviationPercentageByTaskEstimateAndActualEffort(task),
        ).toBe(200);
    });
});
