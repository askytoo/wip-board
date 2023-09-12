import type { Task } from "@/types/task";

/**
 * @param Task task - 完了済みのタスク
 * @returns string - 今日のタスクに追加された日時
 * @description 完了済みのタスクから、今日のタスクに追加された日時を取得する
 * @example getAddedTodayTaskAtFromTask(task);
 */
const getAddedTodayTaskAtFromTask = (task: Task): string => {
    // task.activitiesの中で、typeが"今日のタスクに追加"ので最新のものを探してcreated_atを返す
    const addedTodayTaskAt = task.activities.find(activity => activity.type === "今日のタスクに追加")?.created_at;
    if (!addedTodayTaskAt) {
        return "未追加";
    }

    return addedTodayTaskAt;
};

export default getAddedTodayTaskAtFromTask;
