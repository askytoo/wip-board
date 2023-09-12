import type { Task } from "@/types/task";
import getCompletedAt from "./getCompletedAtFromTask";

/**
 * @param task - Task
 * @returns number - タスクの期日と完了日時の差(単位: 時間)
 * @description タスクの期日と完了日時の差を返す。タスクが完了していなければ"未完了"を返す。
 * @example calculateHoursBetweenCompletedAtAndDeadline(task)
 */
const calculateHoursBetweenCompletedAtAndDeadline = (task: Task): number => {
    const completedAt = getCompletedAt(task);

    if (completedAt === "未完了") {
        return NaN;
    }

    const completedAtDate = new Date(completedAt);
    const deadlineDate = new Date(task.deadline.full);
    // 小数点第2位を四捨五入する
    const hoursBetween = Math.floor(
        (deadlineDate.getTime() - completedAtDate.getTime()) / 1000 / 60 / 60
    );

    return hoursBetween;
};

export default calculateHoursBetweenCompletedAtAndDeadline;
