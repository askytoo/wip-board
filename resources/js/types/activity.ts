export type Activity = {
    id: string;
    tasks_id: string;
    type: Type;
    created_at: string;
    updated_at: string;
};


type Type =
| "今日のタスクに追加"
| "今日のタスクから削除"
| "着手"
| "再開"
| "保留"
| "完了"

