import calculateHoursBetweenCompletedAtAndDeadline from "./calculateHoursBetweenCompletedAtAndDeadlinea";
import { describe,expect,test } from "vitest";

describe("calculateHoursBetweenCompletedAtAndDeadline", () => {
    test("タスクが完了していなければNaNを返す", () => {
        const task = {
            id: 1,
            title: "タスク1",
            deadline:{
                full: "2021-09-01 12:00",
            },
            activities: [
                {
                    id: 1,
                    task_id: 1,
                    type: "着手",
                    created_at: "2021-09-01 10:00",
                },
            ],
        };
        const hoursBetween = calculateHoursBetweenCompletedAtAndDeadline(task);
        expect(hoursBetween).toBeNaN();
    });

    test("タスクが完了していれば期日と完了日時の差を返す", () => {
        const task = {
            id: 1,
            title: "タスク1",
            deadline:{
                full: "2021-09-01 13:30",
            },
            activities: [
                {
                    id: 1,
                    task_id: 1,
                    type: "着手",
                    created_at: "2021-09-01 10:00",
                },
                {
                    id: 4,
                    task_id: 1,
                    type: "完了",
                    created_at: "2021-09-01 15:00",
                },
            ],
        };

        const hoursBetween = calculateHoursBetweenCompletedAtAndDeadline(task);
        expect(hoursBetween).toBe(-2);
    });
});
