import getCompletedAt from "./getCompletedAtFromTask";
import type { Task } from "@/types/task";


/**
 * @param Task[] tasks - 完了しているタスクの配列
 * @param string startDate - フィルタリングの開始日
 * @param string endDate - フィルタリングの終了日
 * @returns Task[] - フィルタリングされたタスクの配列
 * @description 完了日でタスクをフィルタリングする
 * @example filterTasksByCompletedAt(tasks, '2020/01/01', '2020/01/31')
 */
const filterTasksByCompletedAt = (tasks: Task[], startDate: string, endDate: string) => {
    return tasks.filter((task) => {
        const completedAt = getCompletedAt(task);
        const completedAtDate = new Date(completedAt);
        const startDateDate = new Date(`${startDate} 00:00:00`);
        const endDateDate = new Date(`${endDate} 23:59:59`);
        return (
            completedAtDate.getTime() >= startDateDate.getTime() &&
            completedAtDate.getTime() <= endDateDate.getTime()
        );
    });
};

export default filterTasksByCompletedAt;
