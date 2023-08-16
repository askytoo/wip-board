import { render, fireEvent, screen } from '@testing-library/svelte';
import Counter from '@/Components/Counter.svelte';

describe('Counter.svelte', async () => {
　// 初期値
  test('カウンターの初期値は0', async () => {
    render(Counter);
    expect(screen.getByText('0')).toBeTruthy();
  });
  test('減算処理', async () => {
    render(Counter);
    // ボタンを定義
    const decreaseButton = screen.getByLabelText('減算');
    // イベントを定義
    await fireEvent.click(decreaseButton);
    const counter = await screen.findByText('-1');
    expect(counter).toBeTruthy();
  });
  test('加算処理', async () => {
    render(Counter);
    const increaseButton = screen.getByLabelText('加算');
    await fireEvent.click(increaseButton);
    const counter = await screen.findByText('1');
    expect(counter).toBeTruthy();
  });
});
