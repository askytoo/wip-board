/**
 * @param Task tasks - 完了済みのタスク
 * @returns number - 保留回数
 * @description タスクの保留回数を返す
 * @example countOnHoldTaskTimes(task)
 */
const countOnHoldTaskTimes = (task: Task) => {
    return task.activities.filter((activity) => activity.type === "保留")
        .length;
};

export default countOnHoldTaskTimes;
