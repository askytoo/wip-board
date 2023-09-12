import type { Task } from "@/types/task";
import getCompletedAt from "./getCompletedAtFromTask";

/**
 * @param Task[] tasks - 完了済みタスクの配列
 * @param "asc" | "desc" order - ソート順
 * @returns Task[] - 完了日時でソートされたタスクの配列
 * @description 完了日時でタスクをソートする
 * @example sortTasksByCompletedAt(tasks, "asc") // 完了日時が古い順でソートされたタスクの配列
 */
const sortTasksByCompletedAt = (
    tasks: Task[],
    order: "asc" | "desc" = "asc",
) => {
    return tasks.sort((a, b) => {
        const aCompletedAt = getCompletedAt(a);
        const bCompletedAt = getCompletedAt(b);

        if (order === "asc") {
            if (aCompletedAt < bCompletedAt) {
                return -1;
            }
            if (aCompletedAt > bCompletedAt) {
                return 1;
            }
            return 0;
        } else {
            if (aCompletedAt < bCompletedAt) {
                return 1;
            }
            if (aCompletedAt > bCompletedAt) {
                return -1;
            }
            return 0;
        }
    });
};

export default sortTasksByCompletedAt;
