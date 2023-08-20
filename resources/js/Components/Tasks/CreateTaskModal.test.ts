import { render, fireEvent, screen } from "@testing-library/svelte";

import CreateTaskModal from "@/Components/Tasks/CreateTaskModal.svelte";

describe("CreateTasksModal.svelte", async () => {
    afterEach(() => {
        vi.restoreAllMocks();
    });

    it("タスク作成モーダルの表示", async () => {
        // CreateTasksModal.svelteのpropsにcreating = trueを渡す
        render(CreateTaskModal, { creating: true });
        expect(screen.getByText("タスク作成")).toBeTruthy();
    });
    it("タスク作成モーダルの入力確認", async () => {
        render(CreateTaskModal, { creating: true });
        // titleを入力
        const title = screen.getByLabelText("タイトル");
        await fireEvent.input(title, {
            target: { value: "タスク作成モーダルの入力" },
        });
        // 入力した値が反映されているか確認
        expect(title.value).toBe("タスク作成モーダルの入力");

        // 期日を入力
        // 期日は日付と時間の2つのinputがあるので、それぞれに入力する
        const today = new Date();
        const tomorrow = new Date(today.setDate(today.getDate() + 1))
            .toISOString()
            .slice(0, 10);
        const deadline_date = screen.getByLabelText("期日日");
        await fireEvent.input(deadline_date, {
            target: { value: tomorrow },
        });
        // 入力した値が反映されているか確認
        expect(deadline_date.value).toBe(tomorrow);

        const deadline_time = screen.getByLabelText("期日時刻");
        await fireEvent.select(deadline_time, {
            target: { value: "12:00" },
        });
        // 入力した値が反映されているか確認
        expect(deadline_time.value).toBe("12:00");

        // 見積作業時間を入力
        const estimated_time = screen.getByLabelText("見積作業時間(分)");
        await fireEvent.input(estimated_time, {
            target: { value: "1" },
        });
        // 入力した値が反映されているか確認
        expect(estimated_time.value).toBe("1");

        // アウトプットを入力
        const output = screen.getByLabelText("アウトプット");
        await fireEvent.input(output, {
           target: { value: "タスク作成モーダルの入力" },
           });
           // 入力した値が反映されているか確認
           expect(output.value).toBe("タスク作成モーダルの入力");


        // 説明を入力
        const description = screen.getByLabelText("詳細");
        await fireEvent.input(description, {
            target: { value: "タスク作成モーダルの入力" },
        });
        // 入力した値が反映されているか確認
        expect(description.value).toBe("タスク作成モーダルの入力");

        // 今日のタスクに追加するかどうかのチェックボックスをチェック
        const is_today_task = screen.getByRole("switch");
        await fireEvent.click(is_today_task);
        // チェックされているか確認
        expect(is_today_task.checked).toBe(true);
    });
});
