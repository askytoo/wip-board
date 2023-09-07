import type { Task } from "@/types/task";

/**
 * @param task - Task
 * @returns string - タスクの着手日時
 * @description タスクの着手日時を返す。タスクが着手していなければ"未着手"を返す。
 */
const getStartedAt = (task: Task): string => {
    // task.activitiesの配列の中でtypeが"着手"のものを探す
    // そのactivityのcreated_atを返す
    // なければ"未着手"を返す
    const startedAt = task.activities.find(
        (activity) => activity.type === "着手",
    )?.created_at;

    if (startedAt === undefined) {
        return "未着手";
    }

    return startedAt;
};

export default getStartedAt;
