export type Task = {
    id: string;
    title: string;
    deadline: {full: string; date: string; time: string;};
    description: string;
    status: TaskStatus;
    started_at: string;
    completed_at: string;
    estimated_effort: number;
    is_today_task: { label: string; boolean: boolean };
    output: string;
    created_at: string;
};

export type TaskStatus =
    | { label: "未着手"; class: "" }
    | { label: "保留中"; class: "orange-500" }
    | { label: "進行中"; class: "green-500" }
    | { label: "完了"; class: "blue-500" };

export type TaskInput = {
    title: string;
    description: string;
    estimated_effort: number;
    is_today_task: boolean;
    output: string;
};
