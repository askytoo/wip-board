import calculateTimeDifferenceInDays from './calculateTimeDifferenceInDays';
import { describe, expect, test } from 'vitest';

describe('calulateTimeDifferenceInDays', () => {
    test('開始日と終了日の差分日数を計算する', () => {
        expect(calculateTimeDifferenceInDays('2021/01/01', '2021/01/31')).toBe(30);
    });
});
