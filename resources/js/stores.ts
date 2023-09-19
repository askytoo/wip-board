import { writable } from "svelte/store";

import type { Task } from "./types/task";
export const editingTask = writable<Task>({} as Task);
export const deletingTask = writable<Task>({} as Task);
export const copyingTask = writable<Task>({} as Task);

export const onHoldAreaTasks = writable<Task[]>([]);
