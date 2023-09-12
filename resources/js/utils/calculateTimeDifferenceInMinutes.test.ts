import calculateTimeDifferenceInMinutes from "./calculateTimeDifferenceInMinutes";
import { describe, expect, test } from "vitest";

describe("calculateTimeDifferenceInMinutes", () => {
    test("開始時刻と終了時刻の時間差を分で返す", () => {
        const result = calculateTimeDifferenceInMinutes("2020/01/01 00:01", "2020/01/02 00:01");
        expect(result).toBe(1440);
    });
});
