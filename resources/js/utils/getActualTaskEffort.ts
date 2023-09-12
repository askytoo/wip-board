import type { Task } from "@/types/task";
import calculateTimeDifferenceInMinutes from "./calculateTimeDifferenceInMinutes";

/**
 * @param Task task - 完了済みのタスク
 * @returns number - タスクの実績工数(分)
 * @description タスクの実績工数を返す
 * @example getActualTaskEffort(task)
 */
const getActualTaskEffort = (task: Task) => {
    let actualEffort = 0;
    let startedAt: string | undefined;
    let onHoldedOrCompletedAt: string | undefined;
    task.activities.forEach((activity) => {
        if (activity.type === "着手" || activity.type === "再開") {
            startedAt = activity.created_at;
        } else if (activity.type === "保留" || activity.type === "完了") {
            onHoldedOrCompletedAt = activity.created_at;
        }

        if (startedAt && onHoldedOrCompletedAt) {
            actualEffort += calculateTimeDifferenceInMinutes(
                startedAt,
                onHoldedOrCompletedAt,
            );
            startedAt = undefined;
            onHoldedOrCompletedAt = undefined;
        }
    });

    return actualEffort;
};

export default getActualTaskEffort;
