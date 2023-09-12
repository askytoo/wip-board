import filterTasksByCompletedAt from "./filterTasksByCompletedAt";
import type { Task } from "@/types/task";

/**
 * @param Task[] tasks - 完了したタスクの配列
 * @param string startDate - カウントの開始日
 * @param string endDate - カウントの終了日
 * @return number - カウントされたタスクの数
 * @description 期間内のタスクをカウントする
 * @example countCompletedTasksByPeriod(tasks, '2020/01/01', '2020/01/31') // 2020/01/01から2020/01/31までに完了したタスクの数
 */
const countCompletedTasksByPeriod = (tasks: Task[], startDate: string, endDate: string): number => {
    const filteredTasks = filterTasksByCompletedAt(tasks, startDate, endDate);
    return filteredTasks.length;
}

export default countCompletedTasksByPeriod;
