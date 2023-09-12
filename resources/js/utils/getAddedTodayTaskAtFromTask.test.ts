// import type { Task } from "@/types/task";
//
// /**
//  * @param Task task - 完了済みのタスク
//  * @returns string - 今日のタスクに追加された日時
//  * @description 完了済みのタスクから、今日のタスクに追加された日時を取得する
//  * @example getAddedTodayTaskAtFromTask(task);
//  */
// const getAddedTodayTaskAtFromTask = (task: Task): string => {
//     // task.activitiesの中で、typeが"今日のタスクに追加"ので最新のものを探してcreated_atを返す
//     const addedTodayTaskAt = task.activities.find(activity => activity.type === "今日のタスクに追加")?.created_at;
//     if (!addedTodayTaskAt) {
//         return "未追加";
//     }
//
//     return addedTodayTaskAt;
// };
//
// このコードのテストを書く

import  getAddedTodayTaskAtFromTask  from "@/utils/getAddedTodayTaskAtFromTask";
import { describe, test, expect } from "vitest";

describe("getAddedTodayTaskAtFromTask", () => {
    test("今日のタスクに追加されていないタスクを渡すと、未追加を返す", () => {
        const task = {
            id: 1,
            name: "task",
            activities: [],
        };

        expect(getAddedTodayTaskAtFromTask(task)).toBe("未追加");
    });

    test("今日のタスクに追加されているタスクを渡すと、追加された日時を返す", () => {
        const task = {
            id: 1,
            name: "task",
            activities: [
                {
                    id: 1,
                    type: "今日のタスクに追加",
                    created_at: "2021/01/01 00:00",
                },
            ],
        };

        expect(getAddedTodayTaskAtFromTask(task)).toBe("2021/01/01 00:00");
    });
});
