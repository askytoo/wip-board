import type { Task } from "@/types/task";
import getCompletedAt from "./getCompletedAtFromTask";

const countCompletedTasksWithinDeadline = (tasks: Task[]) => {
    return tasks.filter((task) => {
        const completedAt = getCompletedAt(task);
        const completedAtDate = new Date(completedAt);
        const deadlineDate = new Date(task.deadline.full);
        return completedAtDate.getTime() <= deadlineDate.getTime();
    }).length;
}

export default countCompletedTasksWithinDeadline;
