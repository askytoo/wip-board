import type { Task } from "@/types/task";

/**
 * @param task - Task
 * @returns string - タスクの完了日時
 * @description タスクの完了日時を返す。タスクが完了していなければ"未完了"を返す。
 */
const getCompletedAt = (task: Task): string => {
    // task.activitiesの配列の中でtypeが"完了"のものを探す
    // そのactivityのcreated_atを返す
    // なければ"未完了"を返す
    const completedAt = task.activities.find(
        (activity) => activity.type === "完了",
    )?.created_at;

    if (completedAt === undefined) {
        return "未完了";
    }

    return completedAt;
};

export default getCompletedAt;
