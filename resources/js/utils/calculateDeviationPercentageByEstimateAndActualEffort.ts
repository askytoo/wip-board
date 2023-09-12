import type { Task } from "@/types/task";
import getActualTaskEffort from "./getActualTaskEffort";

/**
 * @param task - 完了済みのタスク
 * @returns number - タスクの見積りと実績工数の偏差率(単位: %)
 * @description タスクの見積りと実績工数の偏差率を返す
 * @example calculateDeviationPercentageByTaskEstimateAndActualEffort(task)
 */
const calculateDeviationPercentageByTaskEstimateAndActualEffort = (
    task: Task,
): number => {
    const estimatedEffort = task.estimated_effort;
    const actualEffort = getActualTaskEffort(task);
    return Math.round((actualEffort / estimatedEffort) * 100);
};

export default calculateDeviationPercentageByTaskEstimateAndActualEffort;
