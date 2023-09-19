import type { Activity } from "./activity";

export type Task = {
    id: string;
    title: string;
    deadline: {full: string; date: string; time: string;};
    description: string;
    status: TaskStatus;
    estimated_effort: number;
    is_today_task: boolean;
    output: string;
    created_at: string;
    activities: Activity[];
};

export type TaskStatus =
    | { label: "未着手"}
    | { label: "保留中"}
    | { label: "進行中"}
    | { label: "完了"}

export type TaskInput = {
    title: string;
    description: string;
    estimated_effort: number;
    is_today_task: boolean;
    output: string;
};
